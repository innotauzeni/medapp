@extends('layouts.app')
@section('title', 'Certificates')
@section('header', 'Certificates')
@section('subheader', 'Issued certificates with verification codes')

@section('header-actions')
    @can('certificates.create')<a href="{{ route('certificates.create') }}" class="btn btn-primary"><i class="bi bi-award me-1"></i> Issue certificate</a>@endcan
@endsection

@section('content')
    <div class="card-er card-er-pad mb-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-6">
                <label class="form-label">Search</label>
                <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" class="form-control" placeholder="Certificate or verification code...">
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Any</option>
                    <option value="issued"  @selected(($filters['status'] ?? '') === 'issued')>Issued</option>
                    <option value="revoked" @selected(($filters['status'] ?? '') === 'revoked')>Revoked</option>
                </select>
            </div>
            <div class="col-md-3"><button class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i> Filter</button></div>
        </form>
    </div>

    <div class="table-wrap">
        <div class="table-responsive">
            <table class="table-er">
                <thead><tr><th>Number</th><th>Student</th><th>Course</th><th>Issued</th><th>Score</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                    @forelse ($paginator as $c)
                        <tr>
                            <td><code>{{ $c->certificate_number }}</code></td>
                            <td>{{ $c->student?->full_name }}</td>
                            <td>{{ $c->course?->title }}</td>
                            <td>{{ optional($c->issued_at)->format('Y-m-d') }}</td>
                            <td>{{ $c->score !== null ? number_format($c->score, 1) . '%' : '—' }}</td>
                            <td>
                                @if ($c->status === 'issued')<span class="badge badge-soft badge-soft-success">Issued</span>
                                @else<span class="badge badge-soft badge-soft-danger">Revoked</span>@endif
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('certificates.show', $c) }}" class="btn btn-soft btn-sm btn-icon-only"><i class="bi bi-eye"></i></a>
                                    <a href="{{ route('certificates.download', $c) }}" class="btn btn-soft btn-sm btn-icon-only" target="_blank"><i class="bi bi-file-earmark-pdf"></i></a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="empty-state"><i class="bi bi-award"></i> No certificates issued yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3 d-flex justify-content-center">{{ $paginator->links() }}</div>
@endsection
