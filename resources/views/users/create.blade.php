@extends('layouts.app')
@section('title', 'New user')
@section('header', 'New user')
@section('content')
    <form method="POST" action="{{ route('users.store') }}">
        @csrf
        @include('users._form', ['user' => null])
    </form>
@endsection
