<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\OrderItem;
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

        $hasPurchased = OrderItem::where('product_id', $product->id)
            ->whereHas('order', function ($q) {
                $q->where('user_id', auth()->id())
                    ->whereIn('status', ['confirmed', 'shipped', 'delivered']);
            })
            ->exists();

        if (! $hasPurchased) {
            return back()->withErrors(['rating' => 'You can only review products you have purchased.']);
        }

        $alreadyReviewed = Comment::where('product_id', $product->id)
            ->where('user_id', auth()->id())
            ->exists();

        if ($alreadyReviewed) {
            return back()->withErrors(['rating' => 'You have already reviewed this product.']);
        }

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
