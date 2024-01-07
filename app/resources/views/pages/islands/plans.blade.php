@extends('components.app')
@section('title', 'やまにてぃ（仮）')
@section('contents')
    <div id="app">
        @include('components.header')
        <plans-page
            :hakoniwa="@js($hakoniwa)"
            :island="@js($island)"
            :plan-candidate="@js($executablePlans)"
            :target-islands="@js($targetIslands)"
            :turn="@js($turn)"
        ></plans-page>
        @include('components.footer')
    </div>
@endsection
