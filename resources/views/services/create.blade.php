@extends('layouts.app')
@section('title', 'New service')
@section('header', 'New service')
@section('content')
    <form method="POST" action="{{ route('services.store') }}" enctype="multipart/form-data">@csrf
        @include('services._form', ['service' => null])
    </form>
@endsection
