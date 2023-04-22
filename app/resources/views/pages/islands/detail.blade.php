@extends('components.app')
@section('title', 'やまにてぃ（仮）')
@section('contents')
    <div id="app">
        @include('components.header')
        @php
            var_dump($hakoniwa);
            var_dump($island);
            var_dump($islandStatus);
//            var_dump($islandTerrain->terrain);
//            var_dump($islandLog);
        @endphp
        <sightseeing-page
            :hakoniwa="@js($hakoniwa)"
            :island="@js($island)"
            :island-status="@js($islandStatus)"
{{--            :island-terrain="@js($islandTerrain->terrain)"--}}
{{--            :island-log="@js($islandLog)"--}}
        ></sightseeing-page>
    </div>
@endsection
