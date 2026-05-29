@extends('layouts.app')
@section('title', 'Edit location')
@section('header', 'Edit location')
@section('subheader', $location->name)
@section('content')
    <form method="POST" action="{{ route('locations.update', $location) }}">@csrf @method('PUT')
        @include('locations._form', ['location' => $location])
    </form>
@endsection
