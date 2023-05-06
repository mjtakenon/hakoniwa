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
            config('services.yahoo.authorization_endpoint') .
            '?' .
            http_build_query(
                array(
                    'state' => $state,
                    'nonce' => $nonce,
                    'response_type' => 'code',
                    'client_id' => config('services.yahoo.client_id'),
                    'redirect_uri' => config('services.yahoo.redirect'),
                    'scope' => 'openid'
                )

            )
        );
    }
}
