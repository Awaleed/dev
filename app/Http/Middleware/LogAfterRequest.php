<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogAfterRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }

    public function terminate($request, $response)
    {

        Log::channel('daily_requests')
            ->info(
                'app.requests',
                json_decode(json_encode([
                    'ip' => $request->ip(),
                    'url' => $request->url(),
                    'request' => $request->all(),
                    'response' => $response,
                ]), true),
            );

        // if ($request->url() == '0') {
        //     # code...
        // } else {
        // }
    }
}
