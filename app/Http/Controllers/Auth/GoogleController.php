<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->user();

        // فحص إذا المستخدم موجود مسبقًا
        $user = User::where('email', $googleUser->getEmail())->first();

        if (!$user) {
            // إذا مش موجود أضفه جديد
            $user = User::updateOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name' => $googleUser->getName(),
                    'password' => bcrypt(uniqid()),
                    'url' => 'https://example.com',
                ]
            );
        }

        Auth::login($user);

        return redirect()->route('home'); // أو أي صفحة تريدها
    }
}