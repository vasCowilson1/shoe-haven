<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Product;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $data = $request->validate([
            'rating' => 'required|integer|between:1,5',
            'content' => 'required|string|max:1000',
        ]);

        Comment::create([
            'product_id' => $product->id,
            'user_id' => auth()->id(),
            'content' => $data['content'],
            'rating' => $data['rating'],
            'status' => 'approved',
        ]);

        return back()->with('status', 'Review submitted successfully!');
    }
}
