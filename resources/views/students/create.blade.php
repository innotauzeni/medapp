@extends('layouts.app')
@section('title', 'New student')
@section('header', 'New student')
@section('content')
    <form method="POST" action="{{ route('students.store') }}" enctype="multipart/form-data">
        @csrf
        @include('students._form', ['student' => null])
    </form>
@endsection
