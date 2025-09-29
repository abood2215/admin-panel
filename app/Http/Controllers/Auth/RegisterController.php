<?php
namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required','string','min:3','max:255'],
            'email'    => ['required','email','max:255','unique:users,email'],
            'password' => ['required','confirmed', Password::min(6)->mixedCase()->numbers()],
            'terms'    => ['accepted'],
        ], [
            'terms.accepted' => 'You must agree to the Terms of Service and Privacy Policy.',
        ]);

        // أنشئ المستخدم مرة واحدة فقط
        User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'url'      => 'https://example.com', // مؤقتاً بما أن العمود NOT NULL
            // 'image' => null,
        ]);

        return redirect()->route('signUp')->with('account_created', true);
    }
}
