<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
{{--    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">--}}
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <title>@yield('title')</title>
    @if(\App::environment('local'))
        @vite(['resources/js/app.ts', 'resources/css/app.css'])
    @else
        <link rel="manifest" href="/build/manifest.json"/>
        <script type="module" src="/build/assets/bundle.js"></script>
        <script type="module" src="/build/assets/bundle2.js"></script>
        <link rel="stylesheet" href="/build/assets/asset.css"/>
{{--        <script src="/build/assets/assets.js"></script>--}}
    @endif
</head>
<body>
    <div>
        @yield('contents')
    </div>
</body>
</html>
