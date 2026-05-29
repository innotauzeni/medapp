@extends('layouts.app')
@section('title', 'New enrolment')
@section('header', 'New enrolment')
@section('content')
    <form method="POST" action="{{ route('enrolments.store') }}">
        @csrf
        @include('enrolments._form', ['enrolment' => null])
    </form>
@endsection
