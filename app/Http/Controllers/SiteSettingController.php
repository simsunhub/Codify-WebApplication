<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Storage;

class SiteSettingController extends Controller
{
    public function index()
    {
        $settings = SiteSetting::all()->pluck('value', 'key');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'site_name'        => 'nullable|string|max:255',
            'site_description' => 'nullable|string',
            'support_email'    => 'nullable|email|max:255',
            'support_phone'    => 'nullable|string|max:50',
            'social_telegram'  => 'nullable|url|max:255',
            'social_instagram' => 'nullable|url|max:255',
            'social_youtube'   => 'nullable|url|max:255',
            'hero_video_url'   => 'nullable|url|max:255',
            'site_logo'        => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'site_favicon'     => 'nullable|image|mimes:ico,png,jpg,jpeg,svg,gif,webp|max:1024',
        ]);

        if ($request->hasFile('site_logo')) {
            // Delete old logo if exists
            $oldLogo = SiteSetting::get('site_logo');
            if ($oldLogo) {
                Storage::disk('public')->delete($oldLogo);
            }
            $path = $request->file('site_logo')->store('settings', 'public');
            SiteSetting::set('site_logo', $path);
        }

        if ($request->hasFile('site_favicon')) {
            // Delete old favicon if exists
            $oldFavicon = SiteSetting::get('site_favicon');
            if ($oldFavicon) {
                Storage::disk('public')->delete($oldFavicon);
            }
            $path = $request->file('site_favicon')->store('settings', 'public');
            SiteSetting::set('site_favicon', $path);
        }

        $fields = [
            'site_name',
            'site_description',
            'support_email',
            'support_phone',
            'social_telegram',
            'social_instagram',
            'social_youtube',
            'hero_video_url',
        ];

        foreach ($fields as $field) {
            if ($request->has($field)) {
                SiteSetting::set($field, $request->input($field));
            }
        }

        return redirect()->back()->with('success', __('messages.settings.updated') ?? 'Settings updated successfully.');
    }
}
