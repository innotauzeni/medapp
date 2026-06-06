<?php

namespace App\Http\Controllers;

use App\Http\Requests\Service\StoreServiceRequest;
use App\Http\Requests\Service\UpdateServiceRequest;
use App\Models\Service;
use App\Services\ServiceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function __construct(private readonly ServiceService $services) {}

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

    public function store(StoreServiceRequest $request): RedirectResponse
    {
        $this->authorize('services.create');
        $this->services->create($request->validated());
        return redirect()->route('services.index')->with('status', 'Service created.');
    }

    public function edit(Service $service): View
    {
        $this->authorize('services.update');
        return view('services.edit', compact('service'));
    }

    public function update(UpdateServiceRequest $request, Service $service): RedirectResponse
    {
        $this->authorize('services.update');
        $this->services->update($service, $request->validated());
        return redirect()->route('services.index')->with('status', 'Service updated.');
    }

    public function destroy(Service $service): RedirectResponse
    {
        $this->authorize('services.delete');
        $this->services->delete($service);
        return back()->with('status', 'Service deleted.');
    }
}
