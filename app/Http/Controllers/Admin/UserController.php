<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['profile', 'roles'])
            ->whereDoesntHave('roles', fn ($role) => $role->where('role', 'admin'))
            ->withCount('orders');

        $search = trim((string) $request->input('search', ''));
        if ($search !== '') {
            $cleaned = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $search);
            $pattern = '%' . strtolower($cleaned) . '%';
            $query->where(function ($q) use ($search, $pattern) {
                if (is_numeric($search)) {
                    $q->where('id', (int) $search);
                }
                $q->orWhereRaw('LOWER(name) LIKE ?', [$pattern])
                  ->orWhereRaw('LOWER(email) LIKE ?', [$pattern])
                  ->orWhereHas('profile', function ($p) use ($search, $pattern) {
                      $p->where('phone', 'like', "%{$search}%")
                        ->orWhereRaw('LOWER(full_name) LIKE ?', [$pattern]);
                  });
            });
        }

        $users = $query->latest()->paginate(20)->withQueryString();
        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load('orders.items');
        return view('admin.users.show', compact('user'));
    }
}
