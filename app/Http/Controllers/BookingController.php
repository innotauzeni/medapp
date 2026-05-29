<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function __construct(private readonly BookingService $bookings) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['q', 'status', 'assigned_to']);
        $paginator = $this->bookings->list($filters, $request->integer('per_page', 15) ?: 15);
        $assignees = User::role(['Admin', 'Account'])->orderBy('name')->get(['id', 'name']);
        return view('bookings.index', compact('paginator', 'filters', 'assignees'));
    }

    public function show(int $booking): View
    {
        $this->authorize('bookings.view');
        $b = $this->bookings->get($booking);
        $assignees = User::role(['Admin', 'Account'])->orderBy('name')->get(['id', 'name']);
        return view('bookings.show', ['booking' => $b, 'assignees' => $assignees]);
    }

    public function updateStatus(Request $request, int $booking): RedirectResponse
    {
        $this->authorize('bookings.update');
        $data = $request->validate([
            'status'  => ['required', 'in:pending,contacted,confirmed,cancelled,converted'],
            'comment' => ['nullable', 'string', 'max:1500'],
        ]);
        $this->bookings->updateStatus($booking, $data['status'], $data['comment'] ?? null);
        return back()->with('status', 'Status updated.');
    }

    public function addNote(Request $request, int $booking): RedirectResponse
    {
        $this->authorize('bookings.update');
        $data = $request->validate(['comment' => ['required', 'string', 'max:1500']]);
        $this->bookings->addNote($booking, $data['comment']);
        return back()->with('status', 'Note added.');
    }

    public function assign(Request $request, int $booking): RedirectResponse
    {
        $this->authorize('bookings.update');
        $data = $request->validate(['assigned_to' => ['nullable', 'exists:users,id']]);
        $this->bookings->assign($booking, $data['assigned_to'] ?? null);
        return back()->with('status', 'Booking assigned.');
    }
}
