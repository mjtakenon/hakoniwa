@extends('components.app')
@section('title', 'やまにてぃ（仮）')
@section('contents')
    <div id="app">
        @include('components.header')
        <settings-page
            :island="@js($island)"
            :change-island-name-price="@js($CHANGE_ISLAND_NAME_PRICE)"
        ></settings-page>
        @include('components.footer')
    </div>
@endsection
