@extends('components.app')
@section('title', 'やまにてぃ（仮）')
@section('contents')
    <div id="app">
        @include('components.header')
        <sightseeing-page
            :hakoniwa="@js($hakoniwa)"
            :island="@js($island)"
        ></sightseeing-page>
    </div>
@endsection
