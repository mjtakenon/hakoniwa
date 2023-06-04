<?php

namespace App\Http\Middleware;

use App\Http\Traits\WebApi;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OnlyDebugMode
{
    use WebApi;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (\App::environment('local') && \APP::hasDebugModeEnabled()) {
            return $next($request);
        }

        if ($request->is('api/*')) {
            $this->notFound()->throwResponse();
        } else {
            abort(404);
        }
    }
}
