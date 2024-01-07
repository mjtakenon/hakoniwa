@extends('components.app')
@section('title', 'やまにてぃ（仮）')
@section('contents')
    <div id="app">
        @include('components.header')
        <islands-page
            :hakoniwa="@js($hakoniwa)"
            :island="@js($island)"
        ></islands-page>
        @include('components.footer')
    </div>
@endsection
