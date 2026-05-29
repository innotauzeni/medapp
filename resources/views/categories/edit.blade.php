@extends('layouts.app')
@section('title', 'Edit category')
@section('header', 'Edit category')
@section('subheader', $category->name)
@section('content')
    <form method="POST" action="{{ route('categories.update', $category) }}">@csrf @method('PUT')
        @include('categories._form', ['category' => $category])
    </form>
@endsection
