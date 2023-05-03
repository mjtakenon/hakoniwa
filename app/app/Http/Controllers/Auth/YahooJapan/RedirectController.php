<?php

namespace App\Http\Controllers\Auth\YahooJapan;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class RedirectController extends Controller
{
    public function get()
    {
        if (\Auth::check()) {
            return redirect(route('home'));
        }

        $state = Str::random(40);
        $nonce = Str::random(40);

        session()->put('state', $state);
        session()->put('nonce', $nonce);

        return redirect(
            env('YAHOO_AUTHORIZATION_ENDPOINT') .
            '?' .
            http_build_query(
                array(
                    'state' => $state,
                    'nonce' => $nonce,
                    'response_type' => 'code',
                    'client_id' => env('YAHOO_CLIENT_ID'),
                    'redirect_uri' => env('YAHOO_REDIRECT_URI'),
                    'scope' => 'openid'
                )

            )
        );
    }
}
