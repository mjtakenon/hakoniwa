<?php

namespace App\Http\Controllers\Auth\Google;

use App\Http\Controllers\Controller;
use App\Models\User;


class CallbackController extends Controller
{
    public function get()
    {
        \Log::debug(__CLASS__ . ' ' . __METHOD__ . ' ' . __LINE__ . PHP_EOL);
        if (\Auth::check()) {
            \Log::debug(__CLASS__ . ' ' . __METHOD__ . ' ' . __LINE__ . PHP_EOL);
            return redirect(route('home'));
        }

        \Log::debug(__CLASS__ . ' ' . __METHOD__ . ' ' . __LINE__ . PHP_EOL);
        $token = \DB::transaction(function () {
            \Log::debug(__CLASS__ . ' ' . __METHOD__ . ' ' . __LINE__ . PHP_EOL);
            $googleUser = \Socialite::driver('google')->user();//->stateless()

            \Log::debug(__CLASS__ . ' ' . __METHOD__ . ' ' . __LINE__ . PHP_EOL);
            $user = User::firstOrCreate([
                'email' => $googleUser->email,
            ], [
                'name' => $googleUser->name,
                'email' => $googleUser->email,
            ]);

            \Log::debug(__CLASS__ . ' ' . __METHOD__ . ' ' . __LINE__ . PHP_EOL);
            $token = $user->createToken('token')->plainTextToken;
            \Auth::login($user);

            \Log::debug(__CLASS__ . ' ' . __METHOD__ . ' ' . __LINE__ . PHP_EOL);
            return $token;
        });

        \Log::debug(__CLASS__ . ' ' . __METHOD__ . ' ' . __LINE__ . PHP_EOL);
        if (\HakoniwaService::isIslandRegistered()) {
            return redirect(config('app.url') . '/islands/' . \Auth::user()->island->id . '/plans');
        } else {
            return redirect(route('home'));
        }
    }
}
