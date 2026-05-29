@extends('layouts.app')
@section('title', 'New location')
@section('header', 'New location')
@section('content')
    <form method="POST" action="{{ route('locations.store') }}">@csrf
        @include('locations._form', ['location' => null])
    </form>
@endsection
