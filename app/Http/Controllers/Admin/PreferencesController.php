<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PreferencesController extends Controller
{
    public function update(Request $request)
    {
        $data = $request->validate([
            'sidebar_collapsed' => ['nullable', 'boolean'],
        ]);

        $request->user()->updateSidebarPrefs([
            'sidebar_collapsed' => $request->boolean('sidebar_collapsed'),
        ]);

        return back()->with('status', 'Admin preferences saved.');
    }

    public function reset(Request $request)
    {
        $request->user()->updateSidebarPrefs([
            'sidebar_collapsed' => false,
        ]);

        return back()->with('status', 'Admin preferences reset.');
    }
}
