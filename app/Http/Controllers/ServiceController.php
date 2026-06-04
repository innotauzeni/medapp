<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(): View
    {
        $this->authorize('services.view');
        $paginator = Service::orderBy('order_index')->paginate(20);
        return view('services.index', compact('paginator'));
    }

    public function create(): View
    {
        $this->authorize('services.create');
        return view('services.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('services.create');
        Service::create($this->validated($request));
        return redirect()->route('services.index')->with('status', 'Service created.');
    }

    public function edit(Service $service): View
    {
        $this->authorize('services.update');
        return view('services.edit', compact('service'));
    }

    public function update(Request $request, Service $service): RedirectResponse
    {
        $this->authorize('services.update');
        $service->update($this->validated($request));
        return redirect()->route('services.index')->with('status', 'Service updated.');
    }

    public function destroy(Service $service): RedirectResponse
    {
        $this->authorize('services.delete');
        $service->delete();
        return back()->with('status', 'Service deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'icon'        => ['required', 'string', 'max:60'],
            'title'       => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:1000'],
            'order_index' => ['nullable', 'integer', 'min:0'],
            'is_active'   => ['nullable', 'boolean'],
        ]);
        $data['is_active']   = $request->boolean('is_active', true);
        $data['order_index'] = (int) ($data['order_index'] ?? 0);
        return $data;
    }
}
