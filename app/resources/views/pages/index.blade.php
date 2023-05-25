@extends('components.app')
@section('title', 'やまにてぃ（仮）')
@section('contents')
    <div id="app">
        @include('components.header')
        <div id="index">
            <h1 class="title">やまにてぃ</h1>
            <hr/>

            <turn-viewer
                :turn="@js($turn->turn)"
                :day="@js($turn->next_turn_scheduled_at->format('Y-m-d'))"
                :time="@js($turn->next_turn_scheduled_at->format('H:i'))"
            ></turn-viewer>

            <h2 class="subtitle">注意事項</h2>
            <hr/>
            <p class="box-secondary">現在開発中のため、多くの機能が未実装です。</p>
            <p class="box-alert">テスト中にデータが消える可能性が高いため、何があっても許せる方のみ登録してください。</p>

            <h2 class="subtitle">遊び方</h2>
            <hr/>
            <p>
                TAITOの島育成ゲームソーシャルネットワークサービス「しまにてぃ」の要素を取り入れた平和系箱庭です。更新は約{{config('app.hakoniwa.turn_update_minutes')}}
                分毎です。 </p>
            <p>フルスクラッチで実装しているため、既存の箱庭と仕様が違う点はご容赦ください。連絡いただければ検討します。 </p>
            <p>
                ログインは（今のところ）Google連携のみ利用できます。メールアドレスとユーザー名をサーバーで保持するので、気になる人は捨て垢の利用を推奨します（パスワードはこちらでは保持しません）。 </p>
            <p>不具合・不明点はツイッターからご連絡ください。 </p>
            <p class="box-error">重複登録は禁止です。1人1島でお願いします。</p>

            <h2 class="subtitle">更新情報</h2>
            <hr/>
            <release-notes></release-notes>

            <h2 class="subtitle mt-20">現在のランキング</h2>
            <hr/>

            @foreach($islands as $index => $island)
                <ranking-viewer
                    :index="@js($index+1)"
                    :island="@js($island)"
                ></ranking-viewer>
            @endforeach

            <log-viewer
                :title="'最近の出来事'"
                :unparsed-logs="@js($logs)"
            ></log-viewer>
        </div>
        @include('components.footer')
    </div>
@endsection
