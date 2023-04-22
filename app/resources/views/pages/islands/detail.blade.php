@extends('components.app')
@section('title', 'やまにてぃ（仮）')
@section('contents')
    <div id="app">
        @include('components.header')
        <sightseeing-page
            :hakoniwa="{{ json_encode($hakoniwa) }}"
            :island="{{ json_encode($island) }}"
            :island-status="{{ json_encode($islandStatus) }}"
            :island-terrain="{{ json_encode($islandTerrain->terrain) }}"
            :island-log="{{ json_encode($islandLog) }}"
        ></sightseeing-page>
    </div>
@endsection
