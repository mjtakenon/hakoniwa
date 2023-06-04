@extends('components.app')
@section('title', 'やまにてぃ（仮）')
@section('contents')
    <div id="app">
        @include('components.header')
        <div id="index">
            <div class="subtitle">メンテナンス中</div>
            <div>現在メンテナンス中です。</div>
        </div>
        @include('components.footer')
    </div>
@endsection
