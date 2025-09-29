<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        // Debug: سجل بداية العملية
        Log::info('🌍 SetLocale Middleware Started', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
        ]);

        // 1) أولاً: تحقق من وجود lang في الـ query string (مثل ?lang=ar)
        $queryLang = $request->query('lang');
        if ($queryLang && in_array($queryLang, ['ar', 'en'])) {
            Session::put('locale', $queryLang);
            cookie()->queue(cookie('locale', $queryLang, 60*24*365));
            App::setLocale($queryLang);
            
            Log::info('✅ Locale set from query parameter', [
                'lang' => $queryLang,
                'session_before' => Session::get('locale'),
            ]);
            
            view()->share('appLocale', $queryLang);
            return $next($request);
        }

        // 2) ثانياً: تحقق من الـ session
        $sessionLang = Session::get('locale');
        if ($sessionLang && in_array($sessionLang, ['ar', 'en'])) {
            App::setLocale($sessionLang);
            
            Log::info('✅ Locale set from session', [
                'lang' => $sessionLang,
            ]);
            
            view()->share('appLocale', $sessionLang);
            return $next($request);
        }

        // 3) ثالثاً: تحقق من الـ cookie
        $cookieLang = $request->cookie('locale');
        if ($cookieLang && in_array($cookieLang, ['ar', 'en'])) {
            Session::put('locale', $cookieLang);
            App::setLocale($cookieLang);
            
            Log::info('✅ Locale set from cookie', [
                'lang' => $cookieLang,
            ]);
            
            view()->share('appLocale', $cookieLang);
            return $next($request);
        }

        // 4) أخيراً: استخدم اللغة الافتراضية
        $defaultLang = config('app.locale', 'en');
        App::setLocale($defaultLang);
        
        Log::info('⚠️ Using default locale', [
            'lang' => $defaultLang,
            'query_lang' => $queryLang,
            'session_lang' => $sessionLang,
            'cookie_lang' => $cookieLang,
        ]);
        
        view()->share('appLocale', $defaultLang);

        return $next($request);
    }
}