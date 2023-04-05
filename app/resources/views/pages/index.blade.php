@extends('components.app')
@section('title', 'hakoniwa')
@section('contents')
    @vite('resources/js/app.ts', 'resources/css/app.css')
    <div id="app">
        @include('components.header')
        <div class="home-wrapper">
            <h1 class="title"> Welcome to islands... </h1>

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
                        <td> {{ $island->islandStatuses->first()->development_points }} </td>
                        <td> {{ $island->islandStatuses->first()->population }} </td>
                        <td> {{ $island->islandStatuses->first()->funds }} </td>
                        <td> {{ $island->islandStatuses->first()->foods }} </td>
                        <td> {{ $island->islandStatuses->first()->resources }} </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
