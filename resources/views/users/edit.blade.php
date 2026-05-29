@extends('layouts.app')
@section('title', 'Edit user')
@section('header', 'Edit user')
@section('subheader', $user->name)
@section('content')
    <form method="POST" action="{{ route('users.update', $user) }}">
        @csrf @method('PUT')
        @include('users._form', ['user' => $user])
    </form>
@endsection
