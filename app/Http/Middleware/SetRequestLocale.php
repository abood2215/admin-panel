<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetRequestLocale
{
    public function handle(Request $request, Closure $next)
    {
        // ?lang -> session -> cookie -> default
        $lang = $request->query('lang');

        if (!in_array($lang, ['ar','en'], true)) {
            $lang = $request->session()->get('locale')
                 ?: $request->cookie('locale')
                 ?: config('app.locale', 'en');
        }

        if (!in_array($lang, ['ar','en'], true)) {
            $lang = 'en';
        }

        App::setLocale($lang);
        view()->share('appLocale', $lang);

        return $next($request);
    }
}
