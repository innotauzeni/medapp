@extends('layouts.app')
@section('title', 'New category')
@section('header', 'New category')
@section('content')
    <form method="POST" action="{{ route('categories.store') }}">@csrf
        @include('categories._form', ['category' => null])
    </form>
@endsection
