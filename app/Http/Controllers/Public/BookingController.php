<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\BookingService;
use App\Services\Cart\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function __construct(
        private readonly BookingService $bookings,
        private readonly CartService $cart,
    ) {}

    public function showForm(): View|RedirectResponse
    {
        $items = $this->cart->detailed();
        if (empty($items)) {
            return redirect()->route('public.cart')->with('error', 'Add a course to your cart first.');
        }
        return view('public.book', [
            'items' => $items,
            'total' => $this->cart->total(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'first_name'   => ['required', 'string', 'max:80'],
            'last_name'    => ['required', 'string', 'max:80'],
            'email'        => ['required', 'email', 'max:150'],
            'phone'        => ['required', 'string', 'max:30'],
            'id_type'      => ['required', 'in:id,passport'],
            'id_number'    => ['nullable', 'string', 'max:50'],
            'organization' => ['nullable', 'string', 'max:150'],
            'city'         => ['nullable', 'string', 'max:100'],
            'notes'        => ['nullable', 'string', 'max:1500'],
            'consent'      => ['accepted'],
        ]);
        unset($data['consent']);

        try {
            $booking = $this->bookings->createFromCart($data, $request);
        } catch (\RuntimeException $e) {
            return redirect()->route('public.cart')->with('error', $e->getMessage());
        }

        return redirect()->route('public.book.confirmation', ['code' => $booking->booking_code]);
    }

    public function confirmation(string $code): View|RedirectResponse
    {
        $booking = $this->bookings->findByCode($code);
        if (!$booking) {
            return redirect()->route('home')->with('error', 'Booking not found.');
        }
        return view('public.book-confirmation', ['booking' => $booking]);
    }
}
