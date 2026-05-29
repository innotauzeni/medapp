@extends('layouts.app')
@section('title', 'Edit schedule')
@section('header', 'Edit schedule')
@section('subheader', $schedule->course?->title . ' · ' . $schedule->start_date->format('d M Y'))
@section('content')
    <form method="POST" action="{{ route('schedules.update', $schedule) }}">@csrf @method('PUT')
        @include('schedules._form', ['schedule' => $schedule])
    </form>
@endsection
