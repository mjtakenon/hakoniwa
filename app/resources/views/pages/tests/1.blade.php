@extends('components.app')
@section('title', 'やまにてぃ（仮）')
@section('contents')
    <div id="app">
        @include('components.header')
        @php
            var_dump($hakoniwa);
        @endphp
    </div>
@endsection
