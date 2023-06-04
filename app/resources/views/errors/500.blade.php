@extends('components.app')
@section('title', 'やまにてぃ（仮）')
@section('contents')
    <div id="app">
        @include('components.header')
        <div id="index">
            <div class="subtitle">500 Internal Server Error</div>
            <div>サーバーエラーが発生しました。しばらく経っても改善しない場合、管理者にお問い合わせください。</div>
            <a href="/">HOMEへ戻る</a>
        </div>
        @include('components.footer')
    </div>
@endsection
