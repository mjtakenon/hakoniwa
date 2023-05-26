<?php

namespace App\Http\Middleware;

use App\Http\Traits\WebApi;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    use WebApi;

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if ($request->is('api/*')) {
            $this->forbidden()->throwResponse();
        }

        if (! $request->expectsJson()) {
            return route(RouteServiceProvider::ROUTE_LOGIN,[],false);
        }
    }
}
