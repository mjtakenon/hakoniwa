@extends('components.app')
@section('title', 'やまにてぃ（仮）')
@section('contents')
    <div id="app">
        @include('components.header')
        <sightseeing-page
            :hakoniwa="[]"
            :island="[]"
            :island-status="[]"
            :island-terrain="[]"
            :island-log="[]"
        ></sightseeing-page>
{{--        <sightseeing-page--}}
{{--            :hakoniwa="{{ $hakoniwa }}"--}}
{{--            :island="{{ $island }}"--}}
{{--            :island-status="{{ $islandStatus }}"--}}
{{--            :island-terrain="{{ $terrain }}"--}}
{{--            :island-log="{{ $islandLog }}"--}}
{{--        ></sightseeing-page>--}}
    </div>
@endsection
