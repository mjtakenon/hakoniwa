<?php

namespace App\Http\Controllers\Auth\Google;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


class CallbackController extends Controller
{
    public function get()
    {
        $token = \DB::transaction(function () {
            $googleUser = \Socialite::driver('google')->user();//->stateless()

            $user = User::firstOrCreate([
                'email' => $googleUser->email,
            ], [
                'name' => $googleUser->name,
                'email' => $googleUser->email,
            ]);

            $token = $user->createToken('api token')->plainTextToken;
            Auth::login($user);

            return $token;
        });

        return redirect(config('app.url'));
    }
}
