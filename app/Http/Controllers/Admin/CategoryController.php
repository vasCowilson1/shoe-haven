<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->orderBy('name')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name',
            'slug' => 'nullable|string|max:100|unique:categories,slug',
        ]);

        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        if ($data['slug'] === '') {
            return back()->withErrors(['slug' => 'The category name must contain letters or numbers.']);
        }
        if (Category::where('slug', $data['slug'])->exists()) {
            return back()->withErrors(['slug' => 'That category URL slug is already in use.']);
        }
        Category::create($data);

        return back()->with('status', 'Category created successfully.');
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name,' . $category->id,
            'slug' => 'nullable|string|max:100|unique:categories,slug,' . $category->id,
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        if ($data['slug'] === '') {
            return back()->withErrors(['slug' => 'The category name must contain letters or numbers.']);
        }
        if (Category::where('slug', $data['slug'])->where('id', '!=', $category->id)->exists()) {
            return back()->withErrors(['slug' => 'That category URL slug is already in use.']);
        }
        $category->update($data);

        return back()->with('status', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->exists()) {
            return back()->withErrors(['category' => 'This category cannot be deleted while it contains products. Move those products to another category first.']);
        }

        $category->delete();
        return back()->with('status', 'Category deleted successfully.');
    }
}
