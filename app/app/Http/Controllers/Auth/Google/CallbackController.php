<?php

namespace App\Http\Controllers\Auth\Google;

use App\Http\Controllers\Controller;
use App\Models\User;


class CallbackController extends Controller
{
    public function get()
    {
        \Log::debug(__METHOD__ . ' ' . __LINE__);
        if (\Auth::check()) {
            \Log::debug(__METHOD__ . ' ' . __LINE__);
            return redirect(route('home'));
        }

        \Log::debug(__METHOD__ . ' ' . __LINE__);
        $token = \DB::transaction(function () {
            \Log::debug(__METHOD__ . ' ' . __LINE__);
            $googleUser = \Socialite::driver('google')->user();//->stateless()

            \Log::debug(__METHOD__ . ' ' . __LINE__);
            $user = User::firstOrCreate([
                'email' => $googleUser->email,
            ], [
                'name' => $googleUser->name,
                'email' => $googleUser->email,
            ]);

            \Log::debug(__METHOD__ . ' ' . __LINE__);
            $token = $user->createToken('token')->plainTextToken;
            \Auth::login($user);

            \Log::debug(__METHOD__ . ' ' . __LINE__);
            return $token;
        });

        \Log::debug(__METHOD__ . ' ' . __LINE__);
        if (\HakoniwaService::isIslandRegistered()) {
            \Log::debug(__METHOD__ . ' ' . __LINE__);
            return redirect(config('app.url') . '/islands/' . \Auth::user()->island->id . '/plans');
        } else {
            \Log::debug(__METHOD__ . ' ' . __LINE__);
            return redirect(route('home'));
        }
    }
}
