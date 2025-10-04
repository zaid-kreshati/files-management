<?php

namespace App\Http\Controllers\web;

use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class LanguageController extends Controller
{
    public function switchLanguage($locale)
    {
        // Ensure the selected language is supported
        if (in_array($locale, ['en', 'ar'])) {
            Session::put('locale', $locale); // Save locale in the session
            App::setLocale($locale);
            Log::info('Language switched to: ' . Session::get('locale'));
        }

        return back(); // Redirect back to the previous page
    }
}
