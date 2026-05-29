<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use App\Services\SettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiteSettingController extends Controller
{
    public function __construct(private readonly SettingsService $settings) {}

    public function edit(): View
    {
        $this->authorize('settings.update');
        $grouped = SiteSetting::orderBy('group')->orderBy('id')->get()->groupBy('group');
        return view('settings.edit', compact('grouped'));
    }

    public function update(Request $request): RedirectResponse
    {
        $this->authorize('settings.update');
        $payload = $request->input('settings', []);
        if (!is_array($payload)) {
            return back()->with('error', 'Invalid payload.');
        }

        foreach ($payload as $key => $value) {
            // Cast empty strings to null for optional fields, but keep "0" / "false"
            if ($value === '') $value = '';
            $this->settings->set($key, $value);
        }

        return back()->with('status', 'Settings saved.');
    }
}
