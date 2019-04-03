<?php

namespace App\Http\Middleware;

use Closure;

class CORS
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        if (config('app.debug')) {
            $headers = [
                'Access-Control-Allow-Origin' => $request->header('Origin'),
                'Access-Control-Allow-Methods' => 'POST, GET, OPTIONS, PUT, DELETE',
                'Access-Control-Allow-Headers' => 'Content-Type, Accept, Authorization, Cookie, X-Requested-With, X-XSRF-TOKEN',
                'Access-Control-Allow-Credentials' => 'true'
            ];
            foreach ($headers as $key => $value) {
                $response->headers->set($key, $value);
            }
        }
        return $response;
    }
}
