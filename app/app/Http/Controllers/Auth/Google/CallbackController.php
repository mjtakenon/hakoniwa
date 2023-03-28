<?php

namespace App\Http\Controllers;

class CallbackController extends Controller
{
    public function get() {
        DB::transaction(function () {
            $google_user = Socialite::driver('google')->user();

            $user = User::firstOrCreate([
                'email' => $google_user->email,
            ], [
                'name' => $google_user->name,
                'email' => $google_user->email,
            ]);

            $token = $user->createToken('api token')->plainTextToken;
            Auth::login($user);

            return $token;
        });
    }
}
