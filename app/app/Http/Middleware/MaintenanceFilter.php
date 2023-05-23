<?php

namespace App\Http\Middleware;

use App\Http\Traits\WebApi;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceFilter
{
    use WebApi;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (config('app.is_system_maintenance', 0)) {
            if ($request->is('api/*')) {
                $this->serviceUnavailable()->throwResponse();
            } else {
                abort(503);
            }
        }

        return $next($request);
    }
}
