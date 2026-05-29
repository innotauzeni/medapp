@extends('layouts.app')
@section('title', 'Edit student')
@section('header', 'Edit student')
@section('subheader', $student->full_name . ' · ' . $student->student_number)
@section('content')
    <form method="POST" action="{{ route('students.update', $student) }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        @include('students._form', ['student' => $student])
    </form>
@endsection
