@extends('components.app')
@section('title', 'やまにてぃ（仮）')
@section('contents')
    <div id="app">
        @include('components.header')
        <sightseeing-page
            :hakoniwa="{{ Js::from($hakoniwa) }}"
            :island="{{ Js::from($island) }}"
            :island-status="{{ Js::from($islandStatus) }}"
            :island-terrain="{{ Js::from($islandTerrain->terrain) }}"
            :island-log="{{ Js::from($islandLog) }}"
        ></sightseeing-page>
    </div>
@endsection
