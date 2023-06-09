<?php

namespace App\Http\Controllers\Web\Auth\YahooJapan;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserAuthentication;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Str;

class CallbackController extends Controller
{
    public function get()
    {
        // ログインチェック
        if (\Auth::check()) {
            return redirect(route(RouteServiceProvider::ROUTE_HOME));
        }

        $client = \YConnectClientBuilderService::build();

        $state = session()->pull('state');
        $nonce = session()->pull('nonce');

        // 検証
        $code = $client->getAuthorizationCode($state);
        $client->requestAccessToken(config('services.yahoo.redirect'), $code);
        $client->verifyIdToken($nonce, $client->getAccessToken());

        // identifierを取得
        $idToken = $client->getIdToken();
        $identifier = $idToken->sub;

        $userAuth = UserAuthentication::where('identifier', $identifier)
            ->where('provider', UserAuthentication::PROVIDER_YAHOO)
            ->first();

        if (!is_null($userAuth)) {
            // 既にYahooJapanでログイン済み
            $user = User::where('id', $userAuth->user_id)->first();
            \Auth::login($user);
        } else {
            // 初回YahooJapanログイン時
            \DB::transaction(function () use ($identifier) {
                // ユーザー情報登録
                $user = User::create();

                // 認証情報登録
                UserAuthentication::create([
                    'provider' => UserAuthentication::PROVIDER_YAHOO,
                    'identifier' => $identifier,
                    'user_id' => $user->getAuthIdentifier()
                ]);

                \Auth::login($user);
            });
        }

        if (\HakoniwaService::isIslandRegistered()) {
            return redirect(config('app.url') . '/islands/' . \Auth::user()->island->id . '/plans');
        } else {
            return redirect(route(RouteServiceProvider::ROUTE_HOME));
        }
    }
}
