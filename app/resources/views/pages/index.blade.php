@extends('components.app')
@section('title', 'やまにてぃ（仮）')
@section('contents')
    <div id="app">
        @include('components.header')
        <div class="home-wrapper">
            <h1 class="title"> やまにてぃ </h1>
            <hr/>
            <h2 class="subtitle"> ターン：{{ $turn->turn }}  </h2>
            <h2 class="subtitle"> 次回更新予定：{{ $turn->next_turn_scheduled_at->format('Y-m-d H:i') }}  </h2>
            <p class="subtitle" style="color: orangered"> 現在開発中のため、多くの機能が未実装です。</p>
            <p class="subtitle" style="color: orangered"> テスト中にデータが消える可能性が高いため、何があっても許せる方のみ登録してください。</p>
            <p> TAITOの島育成ゲームソーシャルネットワークサービス「しまにてぃ」の要素を取り入れた平和系箱庭です。更新は約{{config('app.hakoniwa.turn_update_minutes')}}分毎です。 </p>
            <p> フルスクラッチで実装しているため、既存の箱庭と仕様が違う点はご容赦ください。連絡いただければ検討します。 </p>
            <p> ログインは（今のところ）Google連携のみ利用できます。メールアドレスとユーザー名をサーバーで保持するので、気になる人は捨て垢の利用を推奨します（パスワードはこちらでは保持しません）。 </p>
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
                @foreach($islands as $index => $island)
                    <tr>
                        <td> {{ $index+1 }} </td>
                        <td> <a href="{{ '/islands/' . $island->id }}"> {{ $island->name }}島 </a> </td>
                        <td> {{ $island->development_points }} pts </td>
                        <td> {{ $island->population }} 人 </td>
                        <td> {{ $island->funds }} 億円 </td>
                        <td> {{ $island->foods }} ㌧ </td>
                        <td> {{ $island->resources }} ㌧ </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <hr/>

            <div>
                <span class="subtitle"> 最近の出来事 </span>
                <div class="pb-4"></div>
{{--                <div v-for="log of $store.state.logs" :key="log.id">--}}
{{--            <span v-for="context of JSON.parse(log.log)" :key="context.text">--}}
{{--                <a v-if="context.hasOwnProperty('link')" :href="context.link" :style="context.style">--}}
{{--                    {{ context.text }}--}}
{{--                </a>--}}
{{--                <span v-else :style="context.style">--}}
{{--                    {{ context.text }}--}}
{{--                </span>--}}
{{--            </span>--}}
{{--                </div>--}}
                @foreach($logs as $log)
                    <div>
                        @foreach(json_decode($log->log) as $context)
                            <span>
                            @if(property_exists($context, 'link'))
                                <a href="{{$context->link}}" style="{{$context->style}}">
                                    {{ $context->text }}
                                </a>
                            @else
                                @if(property_exists($context, 'style'))
                                    <span style="{{$context->style}}">
                                        {{ $context->text }}
                                    </span>
                                @else
                                    <span>{{ $context->text }}</span>
                                @endif
                            @endif
                            </span>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
