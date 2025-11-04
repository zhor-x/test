<?php

namespace App\Http\Middleware;

use App\Models\Language;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{

    public function handle(Request $request, Closure $next): Response
    {
        $lang = $request->route('lang');

        if (Language::where('country_code', $lang)->exists()) {
            app()->setLocale($lang);
        } else {
            app()->setLocale(config('app.locale')); // fallback to default
        }

        return $next($request);
    }
}
