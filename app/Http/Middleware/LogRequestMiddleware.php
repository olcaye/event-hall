<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogRequestMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $method = $request->method();

        if (in_array($method, ['POST', 'PUT', 'DELETE'])) {
            $logData = [
                'method'  => $method,
                'url'     => $request->fullUrl(),
                'ip'      => $request->ip(),
                'input'   => $request->except(['password', 'password_confirmation', '_token', '_method']),
                'user_id' => $request->user() ? $request->user()->id : 'N/A',
            ];

            Log::channel('requests')->info('HTTP Request Logged', $logData);
        }

        return $next($request);
    }
}
