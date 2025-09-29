<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class PasswordController extends Controller
{
    // GET: عرض نموذج تغيير كلمة المرور
    public function edit()
    {
        return view('livewire.password-edit');
    }

    // POST: معالجة تغيير كلمة المرور
    public function update(Request $request)
    {
        // 1) تحقق من صحة المدخلات
        $request->validate([
            'current_password'      => ['required','string'],
            'new_password'          => ['required','string','min:8','confirmed'],
        ]);

        $user = Auth::user();

        // 2) ابحث إذا current_password صحيح
        if (! Hash::check($request->current_password, $user->password)) {
            return redirect()
                ->route('password.edit')
                ->with('error', 'كلمة المرور الحالية غير صحيحة.');
        }

        // 3) إذا صح، حدّث كلمة المرور
        $user->password = Hash::make($request->new_password);
        $user->save();

        // 4) أعد توجيه GET مع رسالة نجاح
        return redirect()
            ->route('password.edit')
            ->with('success', 'Password changed successfully!');
    }
}
