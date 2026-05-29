<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function index(): View
    {
        $this->authorize('locations.view');
        $paginator = Location::withCount('schedules')->orderBy('name')->paginate(20);
        return view('locations.index', compact('paginator'));
    }

    public function create(): View
    {
        $this->authorize('locations.create');
        return view('locations.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('locations.create');
        $data = $this->validated($request);
        Location::create($data);
        return redirect()->route('locations.index')->with('status', 'Location created.');
    }

    public function edit(Location $location): View
    {
        $this->authorize('locations.update');
        return view('locations.edit', compact('location'));
    }

    public function update(Request $request, Location $location): RedirectResponse
    {
        $this->authorize('locations.update');
        $location->update($this->validated($request));
        return redirect()->route('locations.index')->with('status', 'Location updated.');
    }

    public function destroy(Location $location): RedirectResponse
    {
        $this->authorize('locations.delete');
        if ($location->schedules()->exists()) {
            return back()->with('error', 'Cannot delete a location with schedules attached.');
        }
        $location->delete();
        return back()->with('status', 'Location deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:150'],
            'address_line1' => ['nullable', 'string', 'max:200'],
            'city'          => ['nullable', 'string', 'max:100'],
            'province'      => ['nullable', 'string', 'max:100'],
            'country'       => ['nullable', 'string', 'max:80'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'is_active'     => ['nullable', 'boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active', true);
        return $data;
    }
}
