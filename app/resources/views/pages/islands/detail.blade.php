@extends('components.app')
@section('title', 'やまにてぃ（仮）')
@section('contents')
    <div id="app">
        @include('components.header')
        <sightseeing-page
            :hakoniwa="@js($hakoniwa)"
            :island="@js($island)"
            :island-status="@js($islandStatus)"
            :island-terrain="@js($islandTerrain->terrain)"
            :island-log="@js($islandLog)"
        ></sightseeing-page>
    </div>
@endsection
