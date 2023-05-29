<?php

namespace App\Http\Controllers\Web\Auth\Google;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserAuthentication;
use App\Providers\RouteServiceProvider;


class CallbackController extends Controller
{
    public function get()
    {
        if (\Auth::check()) {
            return redirect(route(RouteServiceProvider::ROUTE_HOME));
        }

        try {
            $googleUser = \Socialite::driver('google')->user();
        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            return redirect(route(RouteServiceProvider::ROUTE_HOME));
        }
        $userAuth = UserAuthentication::where('identifier', $googleUser->id)->where('provider', UserAuthentication::PROVIDER_GOOGLE)->first();

        if (!is_null($userAuth)) {
            // 既にGoogleでログイン済み
            $user = User::where('id', $userAuth->user_id)->first();
            \Auth::login($user);
        } else {
            // 初回Googleログイン時
            \DB::transaction(function () use ($googleUser) {
                // ユーザー情報登録
                $user = User::create();

                // 認証情報登録
                UserAuthentication::create(
                    [
                        'provider' => UserAuthentication::PROVIDER_GOOGLE,
                        'identifier' => $googleUser->id,
                        'user_id' => $user->getAuthIdentifier()
                    ]
                );

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
