<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SettingsService;

class SettingsController extends Controller
{
    public function edit(SettingsService $settings)
    {
        return view('admin.settings.edit', [
            'settings' => $settings,
            'values' => [
                'institute_name' => $settings->get('institute.name', config('app.name', 'Learning Platform')),
                'primary_color' => $settings->get('brand.primary_color', '#0d6efd'),
                'logo_path' => $settings->get('brand.logo_path'),
            ],
        ]);
    }

    public function update(Request $request, SettingsService $settings)
    {
        $validated = $request->validate([
            'institute_name' => ['required', 'string', 'max:120'],
            'primary_color' => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ]);

        $primaryColor = $validated['primary_color'] ?? null;
        if (! is_string($primaryColor) || $primaryColor === '') {
            $primaryColor = '#0d6efd';
        }

        $updates = [
            'institute.name' => $validated['institute_name'],
            'brand.primary_color' => $primaryColor,
        ];

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->storeAs(
                'branding',
                'logo-'.now()->format('YmdHis').'.'.$request->file('logo')->getClientOriginalExtension(),
                'public'
            );

            $updates['brand.logo_path'] = $path;
        }

        $settings->setMany($updates, group: 'brand', isPublic: true);

        return redirect()
            ->route('admin.settings.edit')
            ->with('status', 'Settings updated.');
    }
}
