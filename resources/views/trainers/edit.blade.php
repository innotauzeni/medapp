@extends('layouts.app')
@section('title', 'Edit trainer')
@section('header', 'Edit trainer')
@section('subheader', $trainer->full_name)
@section('content')
    <form method="POST" action="{{ route('trainers.update', $trainer) }}">
        @csrf @method('PUT')
        @include('trainers._form', ['trainer' => $trainer])
    </form>
@endsection
