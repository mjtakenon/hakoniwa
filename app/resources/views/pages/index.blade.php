@extends('components.app')
@section('title', 'やまにてぃ（仮）')
@section('contents')
    <div id="app">
        @include('components.header')
        <div class="home-wrapper">
            <h1 class="title"> やまにてぃ </h1>
            <h2 class="subtitle"> ターン: {{ $turn->turn }}  </h2>
            <h2 class="subtitle"> 次回更新 {{ $turn->next_turn_scheduled_at }}  </h2>
            <p> TAITOの島育成ゲームソーシャルネットワークサービス「しまにてぃ」の要素を取り入れた平和系箱庭です。更新は1時間ごとです。 </p>
            <p> フルスクラッチで実装しているため、既存の箱庭と仕様が違う点はご容赦ください。 </p>
            <p> 管理人のお財布事情により閉じるかもしれない点もご容赦ください。 </p>
            <p> ログインは（今のところ）Google連携のみ利用できます。メールアドレスとユーザー名を取得するので、気になる人は捨て垢の利用を推奨します。 </p>
            <p> 不具合・不明点はツイッターからご連絡ください。 </p>
            <p style="color: orangered">重複登録は禁止です。1人1島でお願いします。</p>

            <hr />
            <table class="table">
                <thead>
                    <tr>
                        <th> 順位 </th>
                        <th> 島 </th>
                        <th> 発展ポイント </th>
                        <th> 人口 </th>
                        <th> 資金 </th>
                        <th> 食料 </th>
                        <th> 資源 </th>
                    </tr>
                </thead>
                <tbody>
                @foreach($islands as $island)
                    <tr>
                        <td> {{ 1 }} </td>
                        <td> <a href="{{ '/islands/' . $island->id }}"> {{ $island->name }} </a> </td>
                        <td> {{ $island->islandStatuses->first()->development_points }} pts </td>
                        <td> {{ $island->islandStatuses->first()->population }} 人 </td>
                        <td> {{ $island->islandStatuses->first()->funds }} 億円 </td>
                        <td> {{ $island->islandStatuses->first()->foods }} ㌧ </td>
                        <td> {{ $island->islandStatuses->first()->resources }} ㌧ </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
