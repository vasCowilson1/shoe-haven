<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category', 'stock');

        $search = trim((string) $request->input('search', ''));
        if ($search !== '') {
            $cleaned = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $search);
            $words = array_values(array_filter(preg_split('/\s+/', $cleaned)));

            $query->where(function ($q) use ($words, $cleaned) {
                if (count($words) > 1) {
                    foreach ($words as $word) {
                        $pattern = '%' . strtolower($word) . '%';
                        $q->where(function ($sub) use ($pattern) {
                            $sub->whereRaw('LOWER(name) LIKE ?', [$pattern])
                                ->orWhereRaw('LOWER(brand) LIKE ?', [$pattern])
                                ->orWhereHas('category', fn ($c) => $c->whereRaw('LOWER(name) LIKE ?', [$pattern]));
                        });
                    }
                } else {
                    $pattern = '%' . strtolower($cleaned) . '%';
                    $q->whereRaw('LOWER(name) LIKE ?', [$pattern])
                      ->orWhereRaw('LOWER(brand) LIKE ?', [$pattern])
                      ->orWhereHas('category', fn ($c) => $c->whereRaw('LOWER(name) LIKE ?', [$pattern]));
                }
            });
        }

        $products = $query->latest()->paginate(30)->withQueryString();
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.products.form', [
            'categories' => $categories,
            'product' => null,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'image' => 'nullable|string|max:500',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:4096',
            'category_id' => 'required|exists:categories,id',
            'is_new' => 'boolean',
            'discount' => 'nullable|integer|min:0|max:100',
            'stock' => 'required|integer|min:0',
        ]);

        $this->validateImageSource($request, $data['image'] ?? null);
        $data['is_new'] = $request->boolean('is_new');
        $data['image'] = $this->resolveImage($request, null);

        $product = Product::create($data);

        ProductStock::create([
            'product_id' => $product->id,
            'quantity' => $data['stock'],
        ]);

        if ($data['stock'] > 0) {
            \App\Models\InventoryHistory::create([
                'product_id' => $product->id,
                'changed_by' => auth()->id(),
                'previous_quantity' => 0,
                'new_quantity' => $data['stock'],
                'change_amount' => $data['stock'],
                'change_type' => 'initial',
                'notes' => 'Initial stock when product was created.',
            ]);
        }

        return redirect()->route('admin.products.index')
            ->with('status', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        $product->load('stock');
        return view('admin.products.form', [
            'categories' => $categories,
            'product' => $product,
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'image' => 'nullable|string|max:500',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:4096',
            'category_id' => 'required|exists:categories,id',
            'is_new' => 'boolean',
            'discount' => 'nullable|integer|min:0|max:100',
            'stock' => 'required|integer|min:0',
        ]);

        $this->validateImageSource($request, $data['image'] ?? null);
        $data['is_new'] = $request->boolean('is_new');

        $oldImage = $product->image;
        $oldStock = (int) ($product->stock?->quantity ?? 0);

        if ($request->hasFile('image_file')) {
            $data['image'] = 'storage/' . $request->file('image_file')->store('products', 'public');
        } elseif ($request->filled('image')) {
            $data['image'] = $request->input('image');
        } else {
            $data['image'] = $product->image;
        }

        DB::transaction(function () use ($product, $data) {
            $product->update($data);
            $product->stock()->updateOrCreate(
                ['product_id' => $product->id],
                ['quantity' => $data['stock']]
            );
        });

        $newStock = (int) $data['stock'];
        if ($newStock !== $oldStock) {
            \App\Models\InventoryHistory::create([
                'product_id' => $product->id,
                'changed_by' => auth()->id(),
                'previous_quantity' => $oldStock,
                'new_quantity' => $newStock,
                'change_amount' => $newStock - $oldStock,
                'change_type' => $newStock > $oldStock ? 'restock' : 'adjustment',
                'notes' => 'Stock changed while editing product.',
            ]);
        }

        if ($data['image'] !== $oldImage && Str::startsWith($oldImage, 'storage/')) {
            Storage::disk('public')->delete(Str::after($oldImage, 'storage/'));
        }

        return redirect()->route('admin.products.index')
            ->with('status', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        if ($product->orderItems()->exists()) {
            return redirect()->route('admin.products.index')
                ->withErrors(['error' => 'This product cannot be deleted because it has been referenced by an order.']);
        }

        $image = $product->image;

        DB::transaction(function () use ($product) {
            $product->delete();
        });

        if (Str::startsWith($image, 'storage/')) {
            Storage::disk('public')->delete(Str::after($image, 'storage/'));
        }

        return redirect()->route('admin.products.index')
            ->with('status', 'Product deleted successfully.');
    }

    private function validateImageSource(Request $request, ?string $image): void
    {
        if ($request->hasFile('image_file') || blank($image)) {
            return;
        }

        $isUrl = filter_var($image, FILTER_VALIDATE_URL) !== false;
        $isStoredPath = Str::startsWith($image, ['storage/', 'images/']);

        if (! $isUrl && ! $isStoredPath) {
            throw ValidationException::withMessages([
                'image' => 'Please provide a valid image URL or a valid local image path.',
            ]);
        }
    }

    private function resolveImage(Request $request, ?Product $product): string
    {
        if ($request->hasFile('image_file')) {
            if ($product) {
                $this->deleteStoredImage($product);
            }

            return 'storage/'.$request->file('image_file')->store('products', 'public');
        }

        if ($request->filled('image')) {
            return $request->input('image');
        }

        if ($product) {
            return $product->image;
        }

        throw ValidationException::withMessages([
            'image_file' => 'Please upload an image or paste an image URL.',
        ]);
    }

    private function deleteStoredImage(?Product $product): void
    {
        if ($product && Str::startsWith($product->image, 'storage/')) {
            Storage::disk('public')->delete(Str::after($product->image, 'storage/'));
        }
    }
}
