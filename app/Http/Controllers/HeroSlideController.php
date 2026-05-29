<?php

namespace App\Http\Controllers;

use App\Models\HeroSlide;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class HeroSlideController extends Controller
{
    public function index(): View
    {
        $this->authorize('hero_slides.view');
        $paginator = HeroSlide::orderBy('order_index')->paginate(20);
        return view('hero_slides.index', compact('paginator'));
    }

    public function create(): View
    {
        $this->authorize('hero_slides.create');
        return view('hero_slides.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('hero_slides.create');
        $data = $this->validated($request);
        $data['image_path'] = $this->handleImage($request);
        HeroSlide::create($data);
        return redirect()->route('hero_slides.index')->with('status', 'Hero slide created.');
    }

    public function edit(HeroSlide $hero_slide): View
    {
        $this->authorize('hero_slides.update');
        return view('hero_slides.edit', ['slide' => $hero_slide]);
    }

    public function update(Request $request, HeroSlide $hero_slide): RedirectResponse
    {
        $this->authorize('hero_slides.update');
        $data = $this->validated($request);
        $path = $this->handleImage($request, $hero_slide);
        if ($path !== null) $data['image_path'] = $path;
        $hero_slide->update($data);
        return redirect()->route('hero_slides.index')->with('status', 'Hero slide updated.');
    }

    public function destroy(HeroSlide $hero_slide): RedirectResponse
    {
        $this->authorize('hero_slides.delete');
        if ($hero_slide->image_path) {
            Storage::disk('public')->delete($hero_slide->image_path);
        }
        $hero_slide->delete();
        return back()->with('status', 'Hero slide deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'badge'              => ['nullable', 'string', 'max:120'],
            'title'              => ['required', 'string', 'max:200'],
            'subtitle'           => ['nullable', 'string', 'max:1000'],
            'image_url'          => ['nullable', 'url', 'max:500'],
            'image_upload'       => ['nullable', 'image', 'max:8192'],
            'cta_label'          => ['nullable', 'string', 'max:60'],
            'cta_url'            => ['nullable', 'string', 'max:300'],
            'secondary_cta_label'=> ['nullable', 'string', 'max:60'],
            'secondary_cta_url'  => ['nullable', 'string', 'max:300'],
            'background_color'   => ['nullable', 'string', 'max:20'],
            'order_index'        => ['nullable', 'integer', 'min:0'],
            'is_active'          => ['nullable', 'boolean'],
        ]);
        $data['is_active']   = $request->boolean('is_active', true);
        $data['order_index'] = (int) ($data['order_index'] ?? 0);
        unset($data['image_upload']);
        return $data;
    }

    private function handleImage(Request $request, ?HeroSlide $existing = null): ?string
    {
        if (!$request->hasFile('image_upload')) return null;
        if ($existing?->image_path) {
            Storage::disk('public')->delete($existing->image_path);
        }
        return $request->file('image_upload')->store('hero-slides', 'public');
    }
}
