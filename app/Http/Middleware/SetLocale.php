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
        $language = Language::resolveByCode($lang);

        if ($language) {
            app()->setLocale(strtolower($lang));
        } else {
            app()->setLocale(config('app.locale')); // fallback to default
        }

        return $next($request);
    }
}
