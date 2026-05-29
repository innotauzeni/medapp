<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\Cart\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(private readonly CartService $cart) {}

    public function show(): View
    {
        return view('public.cart', [
            'items' => $this->cart->detailed(),
            'total' => $this->cart->total(),
            'count' => $this->cart->count(),
        ]);
    }

    public function add(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'course_id'   => ['required', 'integer', 'exists:courses,id'],
            'schedule_id' => ['nullable', 'integer', 'exists:course_schedules,id'],
            'quantity'    => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);
        $this->cart->add(
            (int) $data['course_id'],
            isset($data['schedule_id']) ? (int) $data['schedule_id'] : null,
            (int) ($data['quantity'] ?? 1),
        );
        return back()->with('status', 'Added to cart.');
    }

    public function update(Request $request, string $key): RedirectResponse
    {
        $data = $request->validate(['quantity' => ['required', 'integer', 'min:0', 'max:50']]);
        $this->cart->update($key, (int) $data['quantity']);
        return back()->with('status', 'Cart updated.');
    }

    public function remove(string $key): RedirectResponse
    {
        $this->cart->remove($key);
        return back()->with('status', 'Item removed.');
    }

    public function clear(): RedirectResponse
    {
        $this->cart->clear();
        return back()->with('status', 'Cart cleared.');
    }

    /** Sync from localStorage when the server session expired. */
    public function sync(Request $request): JsonResponse
    {
        $data = $request->validate([
            'items'                   => ['array'],
            'items.*.course_id'       => ['required', 'integer'],
            'items.*.schedule_id'     => ['nullable', 'integer'],
            'items.*.quantity'        => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);
        $this->cart->sync($data['items'] ?? []);
        return response()->json([
            'count' => $this->cart->count(),
            'items' => array_values($this->cart->items()),
        ]);
    }
}
