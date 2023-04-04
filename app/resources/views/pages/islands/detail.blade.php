@extends('components.app')
@section('title', 'hakoniwa')
@section('contents')
    @vite('resources/js/app.ts', 'resources/css/app.css')
    <div id="app">
        @include('components.header')
        <sightseeing-page
            :hakoniwa="{{ $hakoniwa }}"
            :island="{{ $island }}"
            :island-status="{{ $islandStatus }}"
            :island-terrain="{{ $islandTerrain->terrain }}"
            :island-log="{{ $islandLog }}"
        ></sightseeing-page>
    </div>
@endsection
