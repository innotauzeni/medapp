@extends('layouts.app')
@section('title', 'Site settings')
@section('header', 'Site settings')
@section('subheader', 'Currency, contact info, and branding shown across the website')

@section('content')
<form method="POST" action="{{ route('settings.update') }}">
    @csrf @method('PATCH')

    @foreach ($grouped as $group => $settings)
        <div class="card-er card-er-pad mb-3">
            <h2 class="h6 mb-3 text-uppercase" style="letter-spacing: .08em; color: var(--er-muted);">{{ ucfirst($group) }}</h2>
            <div class="row g-3">
                @foreach ($settings as $s)
                    <div class="col-md-6">
                        <label class="form-label">{{ $s->label ?? $s->key }}</label>

                        @if ($s->key === 'currency_symbol_position')
                            <select name="settings[{{ $s->key }}]" class="form-select">
                                <option value="before" @selected($s->value === 'before')>Before amount (e.g. R 100)</option>
                                <option value="after"  @selected($s->value === 'after')>After amount (e.g. 100 R)</option>
                            </select>

                        @elseif ($s->key === 'currency_code')
                            <select name="settings[{{ $s->key }}]" class="form-select">
                                @php
                                    $currencies = [
                                        'ZAR' => 'ZAR — South African Rand',
                                        'ZWL' => 'ZWL — Zimbabwean Dollar',
                                        'USD' => 'USD — US Dollar',
                                        'GBP' => 'GBP — British Pound',
                                        'EUR' => 'EUR — Euro',
                                        'BWP' => 'BWP — Botswana Pula',
                                        'NAD' => 'NAD — Namibian Dollar',
                                        'MZN' => 'MZN — Mozambican Metical',
                                        'ZMW' => 'ZMW — Zambian Kwacha',
                                        'KES' => 'KES — Kenyan Shilling',
                                        'NGN' => 'NGN — Nigerian Naira',
                                        'GHS' => 'GHS — Ghanaian Cedi',
                                    ];
                                @endphp
                                @foreach ($currencies as $code => $label)
                                    <option value="{{ $code }}" @selected($s->value === $code)>{{ $label }}</option>
                                @endforeach
                            </select>

                        @elseif ($s->type === 'bool')
                            <select name="settings[{{ $s->key }}]" class="form-select">
                                <option value="1" @selected($s->cast_value)>Yes</option>
                                <option value="0" @selected(!$s->cast_value)>No</option>
                            </select>

                        @else
                            <input name="settings[{{ $s->key }}]" value="{{ $s->value }}" class="form-control"
                                   @if ($s->type === 'int') type="number" @endif>
                        @endif

                        @if ($s->description)<div class="form-text">{{ $s->description }}</div>@endif
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach

    <div class="d-flex justify-content-end gap-2 mt-3">
        <button class="btn btn-primary"><i class="bi bi-save me-1"></i> Save settings</button>
    </div>
</form>
@endsection
