@extends('components.app')
@section('title', 'やまにてぃ（仮）')
@section('contents')
    <div id="app">
        @include('components.header')
        <div class="home-wrapper">
            <div class="subtitle">セッション期限切れ</div>
            <div>セッションの有効期限が切れました。再度お試しください。</div>
            <a href="/">HOMEへ戻る</a>
        <div class="home-wrapper">
    </div>
@endsection
