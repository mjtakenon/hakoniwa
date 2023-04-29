# やまにてぃ🏝

## 環境構築手順

### 初回起動

#### .envの作成

- 以下を実行し.envを作成

```shell
cp app/.env.local app/.env
```

- `GOOGLE_CLIENT_SECRET`の値をシークレットマネージャから取得しセット

#### コンテナのビルド, モジュールのインストールと起動

```makefile
$ make setup
```

### 2回目以降の起動

```makefile
$ make start
```

### 終了

```makefile
$ make down
```