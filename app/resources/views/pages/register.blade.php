@extends('components.app')
@section('title', '島を探しに行く')
@section('contents')
    <section id="register" class="theme-light">
        <h1 class="title">島を探しに行く</h1>
        <form method="POST" name="register" action="{{ config('app.url').'/register' }}">
            @csrf
            <div class="field">
                <label class="label">島にどんな名前をつけますか?（最大32文字）</label>
                <div class="control">
                    <div class="icons">
                        <span>🏝</span>
                        <span>島</span>
                    </div>
                    <input
                        id="island-name-input"
                        class="input-with-icon"
                        type="text"
                        name="island_name"
                        placeholder="島名を入力してください"
                        maxlength="32"
                        minlength="1"
                        required
                        pattern=".*\S+.*"
                        value="{{old('island_name')}}"
                    >

                </div>
{{--                <p class="help is-success">この島名は利用可能です！</p>--}}
                {{--                <p class="help is-success">名前は後から変更可能です</p>--}}
            </div>

            <div class="field">
                <label class="label">あなたのお名前は?（最大32文字）</label>
                <div class="control has-icons-left has-icons-right">
                    <input
                        id="owner-name-input"
                        class="input"
                        type="text"
                        name="owner_name"
                        placeholder="お名前を入力してください"
                        maxlength="32"
                        minlength="1"
                        required
                        pattern=".*\S+.*"
                        value="{{old('owner_name')}}"
                    >
                    <span class="icon is-small is-left"></span>
                </div>
            </div>

            @error('message')
                <div class="text-error text-center mb-4 font-bold">{{$message}}</div>
            @enderror

            <div class="field">
                <div class="control">
                    <div class="w-full text-center">
                        <input id="submit-button" class="button-primary" type="submit" value="島を探しに行く"/>
                    </div>
                </div>
            </div>
        </form>
    </section>
@endsection
