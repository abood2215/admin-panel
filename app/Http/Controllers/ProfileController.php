<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * أظهر صفحة الملف الشخصي
     */
    public function show()
    {
        return view('livewire.profile-show');// تأكد من مسار الـ Blade الصحيح
    }

    /**
     * أظهر صفحة تعديل الملف الشخصي
     * (إذا كنت لا تستخدم view() في الراوت مباشرة)
     */
    public function edit()
    {
        return view('livewire.profile-edit');
        
    }

    /**
     * معالجة تحديث بيانات الملف الشخصي
     */
    public function update(Request $request)
    {
        // 1) Validation
        $data = $request->validate([
            'name'  => ['required','string','max:255'],
            // إن أردت السماح بتغيير الصورة أو رابط شخصي، أضف القوانين هنا
        ]);

        // 2) احصل على المستخدم الحالي وعدّل بياناته
        $user = Auth::user();
        $user->name = $data['name'];
        // إذا كنت تسمح بتعديل أي حقل آخر، خرّجه من $data وعالجه هنا
        $user->save();

        // 3) إرجاع مع رسالة نجاح
        return back()->with('success', 'Profile updated successfully!');
    }
}
