@extends('components.app')
@section('title', 'やまにてぃ（仮）')
@section('contents')
    <div id="app">
        @include('components.header')
        <index-page
            turn="{{$turn->turn}}"
            day="{{$turn->next_turn_scheduled_at->format('Y-m-d')}}"
            time="{{$turn->next_turn_scheduled_at->format('H:i')}}"
            update-interval="{{config('app.hakoniwa.turn_update_minutes')}}"
            :ranking-islands="@js($islands)"
            :logs="@js($logs)"
        ></index-page>
        @include('components.footer')
    </div>
@endsection
