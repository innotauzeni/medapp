@extends('layouts.app')
@section('title', 'Edit service')
@section('header', 'Edit service')
@section('subheader', $service->title)
@section('content')
    <form method="POST" action="{{ route('services.update', $service) }}" enctype="multipart/form-data">@csrf @method('PUT')
        @include('services._form', ['service' => $service])
    </form>
@endsection
