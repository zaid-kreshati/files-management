<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
class LanguageMiddleware
{
    public function handle($request, Closure $next)
    {
        // Check if a language is set in the session; default to 'en'
        $locale = Session::get('locale', 'en');
        App::setLocale($locale);
        
        Log::info('LanguageMiddleware executed for route: ' . $request->path());
        Log::info('Session locale: ' . Session::get('locale', 'not set'));
        Log::info('App locale: ' . App::getLocale());

        return $next($request);
    }
}

