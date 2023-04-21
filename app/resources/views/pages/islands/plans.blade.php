@extends('components.app')
@section('title', 'やまにてぃ（仮）')
@section('contents')
    <div id="app">
        @include('components.header')
        島エディットコメントアウト中
{{--        <plan-page--}}
{{--            :hakoniwa="{{ $hakoniwa }}"--}}
{{--            :island="{{ $island }}"--}}
{{--            :island-status="{{ $islandStatus }}"--}}
{{--            :island-plans="{{ $islandPlans }}"--}}
{{--            :island-terrain="{{ $islandTerrain->terrain }}"--}}
{{--            :island-log="{{ $islandLog }}"--}}
{{--            :plan-list="{{ json_encode(\PlanService::getAllPlans()) }}"--}}
{{--        ></plan-page>--}}
    </div>
@endsection
