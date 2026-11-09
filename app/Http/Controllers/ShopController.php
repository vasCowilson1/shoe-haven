<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::withCount('products')->get();
        $brands = Product::distinct()->pluck('brand')->sort()->values();

        $query = Product::with(['category', 'sizeStock', 'comments']);

        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->string('category'));
            });
        }

        if ($request->filled('brand')) {
            $query->where('brand', $request->string('brand'));
        }

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
                                ->orWhereHas('category', fn ($c) => $c->whereRaw('LOWER(name) LIKE ?', [$pattern]))
                                ->orWhereHas('description', fn ($d) => $d->whereRaw('LOWER(description) LIKE ?', [$pattern]));
                        });
                    }
                } else {
                    $pattern = '%' . strtolower($cleaned) . '%';
                    $q->whereRaw('LOWER(name) LIKE ?', [$pattern])
                      ->orWhereRaw('LOWER(brand) LIKE ?', [$pattern])
                      ->orWhereHas('category', fn ($c) => $c->whereRaw('LOWER(name) LIKE ?', [$pattern]))
                      ->orWhereHas('description', fn ($d) => $d->whereRaw('LOWER(description) LIKE ?', [$pattern]));
                }
            });
        }

        $sort = $request->string('sort', 'latest');
        $allowedSorts = ['latest', 'oldest', 'price_asc', 'price_desc', 'name_asc', 'name_desc'];
        $sort = in_array($sort, $allowedSorts) ? $sort : 'latest';

        switch ($sort) {
            case 'oldest': $query->oldest(); break;
            case 'price_asc': $query->orderBy('price'); break;
            case 'price_desc': $query->orderBy('price', 'desc'); break;
            case 'name_asc': $query->orderBy('name'); break;
            case 'name_desc': $query->orderBy('name', 'desc'); break;
            default:
                if ($search !== '') {
                    $exactPattern = '%' . strtolower($search) . '%';
                    $query->orderByRaw(
                        'CASE WHEN LOWER(name) LIKE ? THEN 1 WHEN LOWER(brand) LIKE ? THEN 2 ELSE 3 END',
                        [$exactPattern, $exactPattern]
                    );
                }
                $query->latest();
        }

        $products = $query->paginate(30)->withQueryString()->onEachSide(1);

        return view('shop.index', [
            'categories' => $categories,
            'brands' => $brands,
            'products' => $products,
            'activeCategory' => $request->string('category')->toString(),
            'activeBrand' => $request->string('brand')->toString(),
            'currentSort' => $sort,
            'searchQuery' => $search,
        ]);
    }

    public function show(Product $product, \Illuminate\Http\Request $request)
    {
        $product->load('category', 'stock', 'description', 'sizeStock', 'images');

        $related = Product::with(['category', 'sizeStock'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->inRandomOrder()
            ->take(4)
            ->get();

        $wishlisted = auth()->check()
            && auth()->user()->wishlist()->where('product_id', $product->id)->exists();

        $reviews = $product->comments()->with('user')->latest()->get();

        // Recently viewed (cookie-based)
        $recent = collect(explode(',', $request->cookie('recently_viewed', '')))
            ->filter()
            ->unique()
            ->take(9)
            ->toArray();
        $recent = array_diff($recent, [(string)$product->id]);
        array_unshift($recent, (string)$product->id);
        $recent = array_slice($recent, 0, 10);
        $cookie = cookie('recently_viewed', implode(',', $recent), 60 * 24 * 7);

        return response()
            ->view('shop.show', [
                'product' => $product,
                'related' => $related,
                'wishlisted' => $wishlisted,
                'reviews' => $reviews,
            ])
            ->cookie($cookie);
    }

    public function recentlyViewed(Request $request)
    {
        $recentIds = collect(explode(',', $request->cookie('recently_viewed', '')))
            ->filter()
            ->unique()
            ->toArray();

        $products = !empty($recentIds)
            ? Product::with(['category', 'sizeStock'])->whereIn('id', $recentIds)->get()
                ->sortBy(fn ($product) => array_search($product->id, $recentIds))->values()
            : collect();

        return view('shop.recently-viewed', compact('products'));
    }
}
