@extends('layouts.app')
@section('title', 'New schedule')
@section('header', 'New schedule')
@section('content')
    <form method="POST" action="{{ route('schedules.store') }}">@csrf
        @include('schedules._form', ['schedule' => null])
    </form>
@endsection
