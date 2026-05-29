@extends('layouts.app')
@section('title', 'New hero slide')
@section('header', 'New hero slide')
@section('content')
    <form method="POST" action="{{ route('hero_slides.store') }}" enctype="multipart/form-data">@csrf
        @include('hero_slides._form', ['slide' => null])
    </form>
@endsection
