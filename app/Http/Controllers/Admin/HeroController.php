<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HeroController extends Controller
{
    public function edit()
    {
        $hero = HeroSection::getActive() ?? new HeroSection();

        return view('admin.hero.edit', compact('hero'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'headline' => 'required|string|max:500',
            'subtitle' => 'required|string|max:2000',
            'button_text' => 'required|string|max:100',
            'button_url' => 'required|string|max:255',
            'secondary_button_text' => 'required|string|max:100',
            'secondary_button_url' => 'required|string|max:255',
            'stat_value_0' => 'nullable|string|max:20',
            'stat_label_0' => 'nullable|string|max:100',
            'stat_value_1' => 'nullable|string|max:20',
            'stat_label_1' => 'nullable|string|max:100',
            'stat_value_2' => 'nullable|string|max:20',
            'stat_label_2' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $hero = HeroSection::getActive();

        if (! $hero) {
            $hero = new HeroSection();
            $hero->is_active = true;
        }

        $data = [
            'headline' => $validated['headline'],
            'subtitle' => $validated['subtitle'],
            'button_text' => $validated['button_text'],
            'button_url' => $validated['button_url'],
            'secondary_button_text' => $validated['secondary_button_text'],
            'secondary_button_url' => $validated['secondary_button_url'],
        ];

        $stats = [];
        for ($i = 0; $i <= 2; $i++) {
            if (! empty($validated["stat_value_$i"]) && ! empty($validated["stat_label_$i"])) {
                $stat = ['value' => $validated["stat_value_$i"], 'label' => $validated["stat_label_$i"]];
                if ($i === 2) {
                    $stat['icon'] = 'star';
                }
                $stats[] = $stat;
            }
        }
        $data['stats'] = $stats;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('images', 'public');
            $data['image'] = 'images/' . basename($path);
        }

        $hero->fill($data);
        $hero->save();

        return redirect()->route('admin.hero.edit')
            ->with('status', 'Hero section updated successfully.');
    }
}
