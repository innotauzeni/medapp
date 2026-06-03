@extends('layouts.app')
@section('title', 'Edit course')
@section('header', 'Edit course')
@section('subheader', $course->code . ' · ' . $course->title)
@section('content')
    <form method="POST" action="{{ route('courses.update', $course) }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        @include('courses._form', ['course' => $course])
    </form>
@endsection
