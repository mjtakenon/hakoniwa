<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
{{--    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">--}}
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <title>@yield('title')</title>
    @php
      $ts = "src/js/app.ts";
      if (\App::environment('local') && \APP::hasDebugModeEnabled() && file_exists(public_path('hot'))) {
        $ts = 'src/js/debug.ts';
      }
    @endphp
    @vite([$ts, 'src/css/app.scss'])
</head>
<body>
    <div>
        @yield('contents')
    </div>
</body>
</html>
