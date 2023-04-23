<?php

namespace App\Http\Controllers\Auth\Google;

use App\Http\Controllers\Controller;
use App\Models\User;


class CallbackController extends Controller
{
    public function get()
    {
        if (\Auth::check()) {
            return redirect(route('home'));
        }

        $token = \DB::transaction(function () {
            $googleUser = \Socialite::driver('google')->user();//->stateless()

            $user = User::firstOrCreate([
                'email' => $googleUser->email,
            ], [
                'name' => $googleUser->name,
                'email' => $googleUser->email,
            ]);

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
