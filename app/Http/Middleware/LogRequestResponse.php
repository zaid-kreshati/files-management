<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class LogRequestResponse
{
    public function handle($request, Closure $next)
    {
        // Log the request to the request_log channel
        Log::channel('request_log')->info('Request:', [
            'url' => $request->url(),
            'method' => $request->method(),
            'headers' => $request->headers->all(),
            'body' => $request->all(),
        ]);

        // Handle the request
        $response = $next($request);

        // Log the response to the response_log channel
        Log::channel('response_log')->info('Response:', [
            'status' => $response->getStatusCode(),
            'headers' => $response->headers->all(),
            'body' => $response->getContent(),
        ]);

        return $response;
    }
}
