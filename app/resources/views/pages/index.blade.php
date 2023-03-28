@extends('components.app')
@section('title', 'hakoniwa')
@section('contents')
    <div id="app">
    @vite('resources/js/app.ts', 'resources/css/app.css')
@endsection