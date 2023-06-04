@extends('components.app')
@section('title', 'やまにてぃ（仮）')
@section('contents')
    <div id="app">
        @include('components.header')
        <div id="index">
            <div class="subtitle">セッション期限切れ</div>
            <div>セッションの有効期限が切れました。再度お試しください。</div>
            <a href="/">HOMEへ戻る</a>
        </div>
        @include('components.footer')
    </div>
@endsection
