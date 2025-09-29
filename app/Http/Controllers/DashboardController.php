<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        // إحصائيات سريعة
        $totalUsers       = User::count();
        $totalAdmins      = User::where('is_admin', 1)->count();
        $totalNonAdmins   = User::where('is_admin', 0)->count();

        // اجلب كل المستندات للإدارة (بدون فلترة على المستخدم)
        // عدّل الأعمدة حسب جدولك إن لزم (title, language, status ...)
        $documents = Document::query()
            ->latest('created_at')
            ->get(['id','title','language','status','created_at']); // لو ما عندك status اشطبها

        return view('livewire.dashboardAdmin', compact(
            'totalUsers',
            'totalAdmins',
            'totalNonAdmins',
            'documents'
        ));
    }
}
