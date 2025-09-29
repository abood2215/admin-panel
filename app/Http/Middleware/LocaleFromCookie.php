<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LocaleFromCookie
{
    public function handle(Request $request, Closure $next)
    {
        // Cookie is already decrypted by EncryptCookies when we're in the web group
        $cookieLang = $request->cookie('locale');
        $lang = in_array($cookieLang, ['ar', 'en']) ? $cookieLang : config('app.locale', 'en');

        App::setLocale($lang);
        // (Optional) share to all views
        view()->share('appLocale', $lang);

        return $next($request);
    }
}
