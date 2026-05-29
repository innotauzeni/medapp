<?php

namespace App\Http\Controllers;

use App\Http\Requests\Trainer\StoreTrainerRequest;
use App\Http\Requests\Trainer\UpdateTrainerRequest;
use App\Services\TrainerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrainerController extends Controller
{
    public function __construct(private readonly TrainerService $trainers) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['q', 'is_active']);
        $paginator = $this->trainers->list($filters, $request->integer('per_page', 15) ?: 15);
        return view('trainers.index', compact('paginator', 'filters'));
    }

    public function create(): View
    {
        $this->authorize('trainers.create');
        return view('trainers.create');
    }

    public function store(StoreTrainerRequest $request): RedirectResponse
    {
        $trainer = $this->trainers->create($request->validated());
        return redirect()->route('trainers.show', $trainer)->with('status', 'Trainer created.');
    }

    public function show(int $trainer): View
    {
        $this->authorize('trainers.view');
        $t = $this->trainers->get($trainer);
        return view('trainers.show', ['trainer' => $t]);
    }

    public function edit(int $trainer): View
    {
        $this->authorize('trainers.update');
        $t = $this->trainers->get($trainer);
        return view('trainers.edit', ['trainer' => $t]);
    }

    public function update(UpdateTrainerRequest $request, int $trainer): RedirectResponse
    {
        $this->trainers->update($trainer, $request->validated());
        return redirect()->route('trainers.show', $trainer)->with('status', 'Trainer updated.');
    }

    public function destroy(int $trainer): RedirectResponse
    {
        $this->authorize('trainers.delete');
        $this->trainers->delete($trainer);
        return redirect()->route('trainers.index')->with('status', 'Trainer deleted.');
    }
}
