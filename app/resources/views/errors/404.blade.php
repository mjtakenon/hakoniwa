@extends('components.app')
@section('title', 'やまにてぃ（仮）')
@section('contents')
    <div id="app">
        @include('components.header')
        <div id="index">
            <div class="subtitle">404 Not Found</div>
            <div>お探しのページは存在しません。</div>
            <a href="/">HOMEへ戻る</a>
        </div>
        @include('components.footer')
    </div>
@endsection
