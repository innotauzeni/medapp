@extends('layouts.app')
@section('title', 'New trainer')
@section('header', 'New trainer')
@section('content')
    <form method="POST" action="{{ route('trainers.store') }}">
        @csrf
        @include('trainers._form', ['trainer' => null])
    </form>
@endsection
