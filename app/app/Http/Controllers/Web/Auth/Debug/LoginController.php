<?php

namespace App\Http\Controllers\Web\Auth\Debug;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserAuthentication;
use App\Providers\RouteServiceProvider;


class LoginController extends Controller
{
    public function get()
    {
        if (\Auth::check()) {
            return redirect(route(RouteServiceProvider::ROUTE_HOME));
        }

        $userId = config('app.hakoniwa.debug.login_using_id');

        if (is_null($userId)) {
            abort(404);
        }

        $user = \Auth::loginUsingId(config('app.hakoniwa.debug.login_using_id'));

        if ($user === false) {
            // 初回Googleログイン時
            \DB::transaction(function () use ($user) {
                // ユーザー情報登録
                $user = User::create();

                // 認証情報登録
                UserAuthentication::create([
                    'provider' => UserAuthentication::PROVIDER_DEBUG,
                    'identifier' => \Str::random(),
                    'user_id' => $user->id,
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
