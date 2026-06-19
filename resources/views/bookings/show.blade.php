@extends('layouts.app')
@section('title', 'Booking ' . $booking->booking_code)
@section('header', $booking->full_name)
@section('subheader', 'Booking ' . $booking->booking_code . ' · received ' . $booking->created_at->format('d M Y, H:i'))

@section('content')
    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card-er card-er-pad">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                    <h2 class="h6 m-0">Customer details</h2>
                    @php $cls = 'status-' . $booking->status; @endphp
                    <span class="badge {{ $cls }}" style="padding:6px 12px;border-radius:999px;">{{ ucfirst($booking->status) }}</span>
                </div>
                <dl class="row mb-0 small">
                    <dt class="col-4 text-muted">Name</dt><dd class="col-8">{{ $booking->full_name }}</dd>
                    <dt class="col-4 text-muted">Email</dt><dd class="col-8"><a href="mailto:{{ $booking->email }}">{{ $booking->email }}</a></dd>
                    <dt class="col-4 text-muted">Phone</dt><dd class="col-8"><a href="tel:{{ $booking->phone }}">{{ $booking->phone }}</a></dd>
                    <dt class="col-4 text-muted">ID</dt><dd class="col-8">{{ strtoupper($booking->id_type) }} · {{ $booking->id_number ?: '—' }}</dd>
                    <dt class="col-4 text-muted">Organization</dt><dd class="col-8">{{ $booking->organization ?: '—' }}</dd>
                    <dt class="col-4 text-muted">City</dt><dd class="col-8">{{ $booking->city ?: '—' }}</dd>
                    @if ($booking->notes)
                        <dt class="col-4 text-muted">Notes</dt><dd class="col-8">{{ $booking->notes }}</dd>
                    @endif
                </dl>
            </div>

            <div class="card-er mt-3">
                <div class="card-er-head"><h2 class="h6 m-0">Courses requested</h2></div>
                <div class="table-responsive">
                    <table class="table-er">
                        <thead><tr><th>Course</th><th>Schedule</th><th>Qty</th><th class="text-end">Subtotal</th></tr></thead>
                        <tbody>
                            @foreach ($booking->items as $i)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $i->course?->title }}</div>
                                        <div class="small text-muted">{{ $i->course?->code }}</div>
                                    </td>
                                    <td>
                                        @if ($i->schedule)
                                            {{ $i->schedule->start_date->format('d M Y') }}
                                            @if ($i->schedule->location)<div class="small text-muted">{{ $i->schedule->location->name }}</div>@endif
                                        @else <span class="text-muted">Open / TBD</span> @endif
                                    </td>
                                    <td>{{ $i->quantity }}</td>
                                    <td class="text-end">{{ format_money($i->unit_price * $i->quantity) }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="3" class="text-end fw-bold">Total</td>
                                <td class="text-end fw-bold">{{ format_money($booking->total_amount) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-er card-er-pad mt-3">
                <h2 class="h6 mb-3">Activity timeline</h2>
                @if ($booking->statusLogs->isEmpty())
                    <p class="text-muted small mb-0">No activity yet.</p>
                @else
                    <div class="timeline">
                        @foreach ($booking->statusLogs as $log)
                            <div class="timeline-item">
                                <div class="d-flex justify-content-between gap-2 flex-wrap">
                                    <div>
                                        <div class="fw-semibold small">
                                            @if ($log->from_status && $log->from_status !== $log->to_status)
                                                {{ ucfirst($log->from_status) }} → <span style="color: var(--er-primary-600)">{{ ucfirst($log->to_status) }}</span>
                                            @else
                                                Note ({{ ucfirst($log->to_status) }})
                                            @endif
                                            @if ($log->user)<span class="text-muted">· {{ $log->user->name }}</span>@endif
                                        </div>
                                        @if ($log->comment)<div class="small">{{ $log->comment }}</div>@endif
                                    </div>
                                    <div class="small text-muted">{{ $log->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="col-lg-4">
            @can('bookings.update')
            <div class="card-er card-er-pad">
                <h2 class="h6 mb-3">Update status</h2>
                <form method="POST" action="{{ route('bookings.status', $booking) }}">
                    @csrf @method('PATCH')
                    <select name="status" class="form-select mb-2">
                        @foreach (['pending','contacted','confirmed','cancelled','converted'] as $s)
                            <option value="{{ $s }}" @selected($booking->status === $s)>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                    <textarea name="comment" rows="3" class="form-control mb-2" placeholder="Optional note..."></textarea>
                    <button class="btn btn-primary w-100"><i class="bi bi-check2-square me-1"></i> Save status</button>
                </form>
            </div>

            <div class="card-er card-er-pad mt-3">
                <h2 class="h6 mb-3">Assign to</h2>
                <form method="POST" action="{{ route('bookings.assign', $booking) }}">
                    @csrf @method('PATCH')
                    <select name="assigned_to" class="form-select mb-2">
                        <option value="">— Unassigned —</option>
                        @foreach ($assignees as $a)
                            <option value="{{ $a->id }}" @selected($booking->assigned_to === $a->id)>{{ $a->name }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-soft w-100"><i class="bi bi-person-check me-1"></i> Assign</button>
                </form>
            </div>

            <div class="card-er card-er-pad mt-3">
                <h2 class="h6 mb-3">Add note</h2>
                <form method="POST" action="{{ route('bookings.note', $booking) }}">
                    @csrf
                    <textarea name="comment" rows="3" class="form-control mb-2" placeholder="Note for the team..." required></textarea>
                    <button class="btn btn-soft w-100"><i class="bi bi-chat-square-text me-1"></i> Add note</button>
                </form>
            </div>
            @endcan

            <div class="card-er card-er-pad mt-3">
                <h2 class="h6 mb-3">Payment</h2>
                @if (empty($payments))
                    <p class="text-muted small mb-2">No payment record found.</p>
                @else
                    @foreach (array_slice($payments, 0, 3) as $payment)
                        @php
                            $isPaid   = strtoupper($payment['status']) === 'PAID';
                            $isPaynow = ($payment['payment_channel']['slug'] ?? '') === 'paynow';
                            $receipt  = $receipts->firstWhere('book_payment_id', $payment['id']);
                        @endphp
                        <div class="d-flex justify-content-between gap-2 align-items-center border-bottom pb-2 mb-2">
                            <div>
                                <div class="fw-semibold small">{{ $payment['payment_channel']['name'] ?? 'Unknown' }}</div>
                                <div class="small text-muted">{{ format_money($payment['total_ammount']) }}</div>
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <span class="badge status-{{ strtolower($payment['status']) }}">{{ $payment['status'] }}</span>
                                @if (!$isPaid && $isPaynow)
                                    @can('bookings.update')
                                        <form method="POST" action="{{ route('bookings.payment.check', $booking) }}" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="payment_id" value="{{ $payment['id'] }}">
                                            <button type="submit" class="btn btn-soft btn-sm py-0 px-1" title="Check Paynow">
                                                <i class="bi bi-arrow-clockwise"></i>
                                            </button>
                                        </form>
                                    @endcan
                                @endif
                                @if ($receipt)
                                    <a href="{{ route('bookings.receipt.download', [$booking, $receipt->id]) }}"
                                       class="btn btn-primary btn-sm py-0 px-1" title="Download receipt" target="_blank">
                                        <i class="bi bi-download"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif
                <a href="{{ route('bookings.payment.show', $booking) }}" class="btn btn-soft btn-sm w-100 mt-1">
                    <i class="bi bi-credit-card me-1"></i> Manage payments
                    @if ($receipts->count() > 0)
                        <span class="badge bg-success ms-1">{{ $receipts->count() }} receipt{{ $receipts->count() > 1 ? 's' : '' }}</span>
                    @endif
                </a>
            </div>

            <div class="card-er card-er-pad mt-3">
                <dl class="row mb-0 small">
                    <dt class="col-5 text-muted">Source</dt><dd class="col-7">{{ $booking->source }}</dd>
                    <dt class="col-5 text-muted">Received</dt><dd class="col-7">{{ $booking->created_at->format('d M Y, H:i') }}</dd>
                    @if ($booking->contacted_at)<dt class="col-5 text-muted">Contacted</dt><dd class="col-7">{{ $booking->contacted_at->format('d M Y') }}</dd>@endif
                    @if ($booking->confirmed_at)<dt class="col-5 text-muted">Confirmed</dt><dd class="col-7">{{ $booking->confirmed_at->format('d M Y') }}</dd>@endif
                </dl>
            </div>
        </div>
    </div>
@endsection
