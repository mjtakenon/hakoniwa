@extends('components.app')
@section('title', 'hakoniwa')
@section('contents')
    @vite('resources/js/app.ts', 'resources/css/app.css')
    <div id="app"></div>
@endsection
