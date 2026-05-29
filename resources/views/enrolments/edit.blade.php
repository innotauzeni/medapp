@extends('layouts.app')
@section('title', 'Edit enrolment')
@section('header', 'Edit enrolment')
@section('content')
    <form method="POST" action="{{ route('enrolments.update', $enrolment) }}">
        @csrf @method('PUT')
        @include('enrolments._form', ['enrolment' => $enrolment])
    </form>
@endsection
