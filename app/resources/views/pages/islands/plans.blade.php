@extends('components.app')
@section('title', 'やまにてぃ（仮）')
@section('contents')
    <div id="app">
        @include('components.header')
        <plan-page
            :hakoniwa="@js($hakoniwa)"
            :island="@js($island)"
            :island-status="@js($islandStatus)"
            :island-plans="@js($islandPlans)"
            :island-terrain="@js($islandTerrain->terrain)"
            :island-log="@js($islandLog)"
            :plan-list="@js(\PlanService::getAllPlans())"
        ></plan-page>
    </div>
@endsection
