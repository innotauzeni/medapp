@extends('layouts.app')
@section('title', 'Edit hero slide')
@section('header', 'Edit hero slide')
@section('subheader', strip_tags($slide->title))
@section('content')
    <form method="POST" action="{{ route('hero_slides.update', $slide) }}" enctype="multipart/form-data">@csrf @method('PUT')
        @include('hero_slides._form', ['slide' => $slide])
    </form>
@endsection
