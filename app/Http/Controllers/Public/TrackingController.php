<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrackingController extends Controller
{
    public function __construct(private readonly BookingService $bookings) {}

    public function form(): View
    {
        return view('public.track-form');
    }

    public function submit(Request $request): RedirectResponse
    {
        $data = $request->validate(['code' => ['required', 'string', 'max:64']]);
        return redirect()->route('track.show', ['code' => trim($data['code'])]);
    }

    public function show(string $code): View
    {
        $booking = $this->bookings->findByCode($code);
        return view('public.track-result', ['booking' => $booking, 'code' => $code]);
    }
}
