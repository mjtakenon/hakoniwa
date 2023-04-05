@extends('components.app')
@section('title', 'hakoniwa')
@section('contents')
    @vite('resources/js/app.ts', 'resources/css/app.css')
    <div id="app">
        @include('components.header')
        <div class="home-wrapper">
            <h1 class="title"> Welcome to islands... </h1>

        </div>
    </div>
@endsection
