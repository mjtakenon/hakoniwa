@extends('components.app')
@section('title', 'やまにてぃ（仮）')
@section('contents')
    <div id="app">
        @include('components.header')
        <releases-page></releases-page>
        @include('components.footer')
    </div>
@endsection
