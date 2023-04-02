@extends('components.app')
@section('title', 'hakoniwa')
@section('contents')
    @vite('resources/js/app.ts', 'resources/css/app.css')
    <div id="app">
        <island-viewer
            :island="{{ $island }}"
            :island-status="{{ $islandStatus }}"
            :island-terrain="{{ $islandTerrain->terrain }}"
            :island-log="{{ $islandLog }}"
        ></island-viewer>
    </div>
@endsection
