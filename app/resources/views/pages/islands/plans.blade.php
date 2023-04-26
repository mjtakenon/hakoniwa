@extends('components.app')
@section('title', 'やまにてぃ（仮）')
@section('contents')
    <div id="app">
        @include('components.header')
        <plan-page
            :hakoniwa="@js($hakoniwa)"
            :island="@js($island)"
            :plan-candidate="@js($executablePlans)"
        ></plan-page>
    </div>
@endsection
