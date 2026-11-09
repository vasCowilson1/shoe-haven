<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function index(Request $request)
    {
        $query = NewsletterSubscriber::latest();

        $search = trim((string) $request->input('search', ''));
        if ($search !== '') {
            $cleaned = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $search);
            $pattern = '%' . strtolower($cleaned) . '%';
            $query->whereRaw('LOWER(email) LIKE ?', [$pattern]);
        }

        if ($request->has('active')) {
            $query->where('is_active', $request->boolean('active'));
        }

        $subscribers = $query->paginate(25)->withQueryString();

        return view('admin.newsletter.index', compact('subscribers'));
    }

    public function toggle(NewsletterSubscriber $subscriber)
    {
        $subscriber->update(['is_active' => ! $subscriber->is_active]);

        return back()->with('status', 'Subscriber status updated.');
    }
}
