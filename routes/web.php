<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\GithubController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\Admin\DocumentAdminController;
use App\Http\Controllers\Admin\LibraryController; // (القديمة/الهرمية - اختياري)
use App\Http\Controllers\ChatbotController;
use App\Http\Middleware\AdminMiddleware;
use App\Models\User;
use App\Http\Controllers\Admin\SpecialtyAdminController;

// NEW (لتصفح مبسّط)
use App\Http\Controllers\Admin\BrowseController;

Route::middleware(['web', \App\Http\Middleware\SetRequestLocale::class])->group(function () {

    // تبديل اللغة
    Route::get('/locale/{lang}', function (Request $request, $lang) {
        if (!in_array($lang, ['ar','en'], true)) { $lang = 'en'; }
        $request->session()->put('locale', $lang);
        cookie()->queue(cookie('locale', $lang, 60 * 24 * 365));
        app()->setLocale($lang);
        return back();
    })->name('locale.set');

    // فحص اللغة
    Route::get('/__test-locale', function (Request $request) {
        return response()->json([
            'query_lang'     => $request->query('lang'),
            'cookie_locale'  => $request->cookie('locale'),
            'session_locale' => $request->session()->get('locale'),
            'app_locale'     => app()->getLocale(),
            'welcome_trans'  => __('Welcome'),
            'all_session'    => $request->session()->all(),
        ]);
    });

    // دردشة
    Route::post('/chat/send', [ChatbotController::class, 'send'])->name('chat.send');

    /* ====================== ضيوف ====================== */
    Route::middleware('guest')->group(function () {
        Route::get('/login',  [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [LoginController::class, 'login'])->name('login.perform');

        Route::view('/signup', 'signup')->name('signUp');
        Route::post('/register', [RegisterController::class, 'register'])->name('register');

        Route::get('/auth/google',          [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
        Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

        Route::get('/auth/github',          [GithubController::class, 'redirectToGithub'])->name('github.login');
        Route::get('/auth/github/callback', [GithubController::class, 'handleGithubCallback']);
    });

    Route::get('/mobile/years', [\App\Http\Controllers\MobileBrowseController::class, 'years'])
        ->name('mobile.years');

    /* ====================== خروج ====================== */
    Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

    /* ====================== مستخدم مسجّل ====================== */
    Route::middleware('auth')->group(function () {
        Route::view('/',        'home')->name('home');
        Route::view('/pricing', 'pricing')->name('pricing');
        Route::view('/about',   'about')->name('about');

        Route::get('/profile',       [ProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit',  [ProfileController::class, 'edit'])->name('profile.edit');
        Route::post('/profile/edit', [ProfileController::class, 'update'])->name('profile.update');

        Route::get('/profile/password',  [PasswordController::class, 'edit'])->name('password.edit');
        Route::post('/profile/password', [PasswordController::class, 'update'])->name('password.update');

        // واجهة المستندات للمستخدم العادي
        Route::get('/documents',                    [DocumentController::class, 'index'])->name('documents.index');
        Route::get('/documents/{document}',         [DocumentController::class, 'view'])->name('documents.view');
        Route::post('/documents/{document}/submit', [DocumentController::class, 'submit'])->name('documents.submit');
    });

    /* ====================== مسؤول ====================== */
    Route::middleware(['auth', AdminMiddleware::class])
        ->prefix('admin')->name('admin.')->group(function () {

            // جعل /admin تهبط مباشرة على /admin/browse
            Route::get('/', function () {
                return redirect()->route('admin.browse.streams');
            })->name('root');

            // إبقاء اسم admin.dashboard كتحويل إلى browse لضمان توافق أي روابط قديمة
            Route::get('/dashboard', function () {
                return redirect()->route('admin.browse.streams');
            })->name('dashboard');

            // CRUD الرفع والتحرير … إلخ
            Route::get ('/documents/upload',                   [DocumentAdminController::class, 'create'])->name('documents.upload');
            Route::post('/documents',                          [DocumentAdminController::class, 'store'])->name('documents.store');
            Route::get ('/documents/{document}/edit',          [DocumentAdminController::class, 'edit'])->name('documents.edit');
            Route::post('/documents/{document}/reextract',     [DocumentAdminController::class, 'reextract'])->name('documents.reextract');
            Route::post('/documents/{document}/reorder',       [DocumentAdminController::class, 'reorder'])->name('documents.reorder');
            Route::post('/documents/{document}/questions',     [DocumentAdminController::class, 'storeQuestion'])->name('documents.storeQuestion');
            Route::post('/documents/{document}/questions/{question}', [DocumentAdminController::class, 'updateQuestion'])->name('documents.updateQuestion');
            Route::delete('/documents/{document}/questions/{question}', [DocumentAdminController::class, 'destroyQuestion'])->name('documents.destroyQuestion');
            Route::delete('/documents/{document}',             [DocumentAdminController::class, 'destroy'])->name('documents.destroy');

            // ====================== Admin Browse: (Stream -> Year+Semester -> Docs) ======================
            Route::prefix('browse')->name('browse.')->group(function () {
                // 1) اختر الفرع (علمي/أدبي)
                Route::get('/', [BrowseController::class, 'streams'])->name('streams');
                // 2) اختر سنة + فصل متاحة لهذا الفرع
                Route::get('/{stream:slug}', [BrowseController::class, 'yearSemesters'])->name('year_semesters');
                // 3) عرض الملفات
                Route::get('/{stream:slug}/{year}/{semester}', [BrowseController::class, 'documents'])->name('documents');
            });

            // (اختياري) المكتبة الهرمية القديمة
            Route::prefix('library')->name('library.')->group(function () {
                Route::get('/',                                   [LibraryController::class,'streams'])->name('streams');
                Route::get('/{stream:slug}',                      [LibraryController::class,'years'])->name('years');
                Route::get('/{stream:slug}/{year}',               [LibraryController::class,'specialties'])->name('specialties');
                Route::get('/{stream:slug}/{year}/{specialty}',   [LibraryController::class,'subjects'])->name('subjects');
                Route::get('/{stream:slug}/{year}/{specialty}/{subject}', [LibraryController::class,'documents'])->name('documents');
            });

            // إدارة المستخدمين
            Route::get('/users', function () {
                $users = User::orderBy('created_at', 'desc')->get();
                return view('livewire.usersAdmin', compact('users'));
            })->name('users');
        });

    /* ====================== APIs مساعدة ====================== */
    Route::get('/api/specialties/{stream:slug}', [LibraryController::class,'apiSpecialties'])->middleware('auth');
    Route::get('/api/subjects/{specialty}',      [LibraryController::class,'apiSubjects'])->middleware('auth');

    // fallback
    Route::fallback(function () {
        return auth()->check() ? redirect()->route('home') : redirect()->route('login');
    });

}); // group
