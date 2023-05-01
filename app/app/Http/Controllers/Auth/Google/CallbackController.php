<?php

namespace App\Http\Controllers\Auth\Google;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserAuthentication;


class CallbackController extends Controller
{
    public function get()
    {
        if (\Auth::check()) {
            return redirect(route('home'));
        }

        $token = \DB::transaction(function () {
            $googleUser = \Socialite::driver('google')->user();//->stateless()

            // ユーザー情報登録
            $user = User::firstOrCreate([
                'email' => $googleUser->email,
            ], [
                'name' => $googleUser->name,
                'email' => $googleUser->email,
            ]);

            // 認証情報登録
            $userAuth = new UserAuthentication();
            $userAuth->provider = UserAuthentication::PROVIDER_GOOGLE;
            $userAuth->identifier = $googleUser->id;
            $userAuth->user_id = $user->getAuthIdentifier();
            $userAuth->save();

            $token = $user->createToken('token')->plainTextToken;
            \Auth::login($user);

            return $token;
        });

        if (\HakoniwaService::isIslandRegistered()) {
            return redirect(config('app.url') . '/islands/' . \Auth::user()->island->id . '/plans');
        } else {
            return redirect(route('home'));
        }
    }
}
