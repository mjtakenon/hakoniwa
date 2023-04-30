@extends('components.app')
@section('title', '島を探しに行く')
@section('contents')
    <section class="section">
        <h1 class="title">島を探しに行く</h1>
        <form method="POST" name="register" action="{{ config('app.url').'/register' }}" style="max-width:400px">
            @csrf
            <div class="field">

                <label class="label">島にどんな名前をつけますか?（最大32文字）</label>
                <div class="control has-icons-left has-icons-right">
                    <input id="island-name-input" class="input" type="text" name="island_name" placeholder="島名を入力してください" maxlength="32" minlength="1" required pattern=".*\S+.*">
                    <span class="icon is-small is-left">
{{--                      <i class="fas fa-user"></i>--}}
                        🏝
                    </span>
                    <span class="icon is-small is-right">
                        島
{{--                      <i class="fas fa-check"></i>--}}
                    </span>
                </div>
{{--                <p class="help is-success">この島名は利用可能です！</p>--}}
                {{--                <p class="help is-success">名前は後から変更可能です</p>--}}
            </div>

            <div class="field">
                <label class="label">あなたのお名前は?（最大32文字）</label>
                <div class="control has-icons-left has-icons-right">
                    <input id="owner-name-input" class="input" type="text" name="owner_name" placeholder="お名前を入力してください" maxlength="32" minlength="1" required pattern=".*\S+.*">
                    <span class="icon is-small is-left"></span>
                </div>
            </div>

            <div class="field">
                <div class="control">
                    <input id="submit-button" class="button is-link" type="submit" value="島を探しに行く"/>
                </div>
            </div>
        </form>
    </section>
@endsection
