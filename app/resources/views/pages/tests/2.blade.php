@extends('components.app')
@section('title', 'やまにてぃ（仮）')
@section('contents')
    <div id="app">
        @include('components.header')
        @php
//            var_dump($hakoniwa);
            var_dump($island);
//            var_dump($islandStatus);
\Log::debug(__METHOD__ . ' ' . __LINE__);
//            var_dump($islandLog);
        @endphp
    </div>
@endsection
