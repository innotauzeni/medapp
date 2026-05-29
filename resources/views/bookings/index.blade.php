@extends('layouts.app')
@section('title', 'Bookings')
@section('header', 'Bookings')
@section('subheader', 'Customer course bookings from the public site')

@section('content')
    <div class="card-er card-er-pad mb-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" class="form-control" placeholder="Code, name, email, organization...">
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All statuses</option>
                    @foreach (['pending','contacted','confirmed','cancelled','converted'] as $s)
                        <option value="{{ $s }}" @selected(($filters['status'] ?? '') === $s)>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Assigned to</label>
                <select name="assigned_to" class="form-select">
                    <option value="">Anyone</option>
                    @foreach ($assignees as $a)
                        <option value="{{ $a->id }}" @selected((int)($filters['assigned_to'] ?? 0) === $a->id)>{{ $a->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2"><button class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i> Filter</button></div>
        </form>
    </div>

    <div class="table-wrap">
        <div class="table-responsive">
            <table class="table-er">
                <thead><tr><th>Code</th><th>Customer</th><th>Items</th><th>Total</th><th>Status</th><th>Assigned</th><th>Received</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                    @forelse ($paginator as $b)
                        @php
                            $init = strtoupper(substr($b->first_name,0,1) . substr($b->last_name,0,1));
                            $cls  = 'status-' . $b->status;
                        @endphp
                        <tr>
                            <td><code>{{ $b->booking_code }}</code></td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <span class="avatar-bubble">{{ $init }}</span>
                                    <div>
                                        <div class="fw-semibold">{{ $b->full_name }}</div>
                                        <div class="small text-muted">{{ $b->email }} · {{ $b->phone }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                {{ $b->items->count() }} course{{ $b->items->count() === 1 ? '' : 's' }}
                                @if ($b->items->isNotEmpty())
                                    <div class="small text-muted">{{ \Illuminate\Support\Str::limit($b->items->first()->course?->title, 30) }}@if ($b->items->count() > 1) +{{ $b->items->count() - 1 }} more @endif</div>
                                @endif
                            </td>
                            <td class="fw-semibold">{{ format_money($b->total_amount) }}</td>
                            <td><span class="badge {{ $cls }}" style="padding:5px 10px;border-radius:999px;">{{ ucfirst($b->status) }}</span></td>
                            <td>{{ $b->assignee?->name ?? '—' }}</td>
                            <td>{{ $b->created_at->diffForHumans() }}</td>
                            <td class="text-end">
                                <a href="{{ route('bookings.show', $b) }}" class="btn btn-soft btn-sm btn-icon-only"><i class="bi bi-eye"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="empty-state"><i class="bi bi-inbox"></i> No bookings yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3 d-flex justify-content-center">{{ $paginator->links() }}</div>
@endsection
