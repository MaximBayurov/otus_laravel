<?php

namespace App\Http\Middleware;

use App\Enums\AllowedLocales;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Locale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = AllowedLocales::tryFrom($request->route('locale'));
        if (empty($locale)) {
            abort(404);
        }

        \App::setLocale($locale->value);
        return $next($request);
    }
}
