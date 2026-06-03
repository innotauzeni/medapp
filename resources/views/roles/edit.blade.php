@extends('layouts.app')
@section('title', 'Edit role')
@section('header', 'Edit role')
@section('subheader', $role->name)
@section('content')
    <form method="POST" action="{{ route('roles.update', $role) }}">
        @csrf @method('PUT')
        @include('roles._form', ['role' => $role])
    </form>
@endsection
