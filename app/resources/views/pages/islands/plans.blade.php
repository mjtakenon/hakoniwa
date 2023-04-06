@extends('components.app')
@section('title', 'hakoniwa')
@section('contents')
    @vite('resources/js/app.ts', 'resources/css/app.css')
    <div id="app">
        @include('components.header')
        <plan-page
            :hakoniwa="{{ $hakoniwa }}"
            :island="{{ $island }}"
            :island-status="{{ $islandStatus }}"
            :island-plans="{{ $islandPlans }}"
            :island-terrain="{{ $islandTerrain->terrain }}"
            :island-log="{{ $islandLog }}"
        ></plan-page>
    </div>
@endsection
