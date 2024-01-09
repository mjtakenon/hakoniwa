# やまにてぃ🏝

[![Laravel](https://github.com/mjtakenon/hakoniwa/actions/workflows/laravel.yml/badge.svg?branch=main)](https://github.com/mjtakenon/hakoniwa/actions/workflows/laravel.yml) ![tag](https://img.shields.io/github/v/tag/mjtakenon/hakoniwa)


## 環境構築手順

### 初回起動

#### .envの作成

- 以下を実行し.envを作成

```shell
cp app/.env.local app/.env
```

- （Googleログインを利用する場合）`GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`の値をGCPコンソールから取得し`.env`にセット
  - https://console.cloud.google.com/apis/credentials
- （Yahoo!ログインを利用する場合）`YAHOO_CLIENT_ID`, `YAHOO_CLIENT_SECRET`の値をYahoo!デベロッパーコンソールから取得し`.env`にセット
  - https://e.developer.yahoo.co.jp/dashboard

#### hostsの設定

- hostsファイルにローカル環境のドメインを追記してください（管理者権限が必要です）
  - Windows：`C:\Windows\System32\drivers\etc\hosts`
  - Mac：`/etc/hosts`

```
127.0.0.1 local-yamanity.net
::1 local-yamanity.net
```

#### コンテナのビルド, モジュールのインストールと起動

- Windows+WSL環境で以下コマンドの実行に権限周りのエラーが出る場合、sudoをつけて実行してください

```sh
$ make init
$ make setup
```

- vite開発サーバーが立ち上がったら、以下URLにブラウザからアクセスすることで、ページが表示できます
  - http://local-yamanity.net:54380

### 2回目以降の起動

```sh
$ make start
```

### 終了

```sh
$ make down
```
