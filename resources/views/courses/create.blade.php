@extends('layouts.app')
@section('title', 'New course')
@section('header', 'New course')
@section('content')
    <form method="POST" action="{{ route('courses.store') }}">
        @csrf
        @include('courses._form', ['course' => null])
    </form>
@endsection
