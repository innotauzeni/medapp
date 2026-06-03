@extends('layouts.app')
@section('title', 'New role')
@section('header', 'New role')
@section('subheader', 'Define a custom role and choose what it can access')
@section('content')
    <form method="POST" action="{{ route('roles.store') }}">
        @csrf
        @include('roles._form', ['role' => null])
    </form>
@endsection
