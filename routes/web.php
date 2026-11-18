<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DepedPdfMappingController;
use App\Http\Controllers\Admin\DepedFormController as AdminDepedFormController;
use App\Http\Controllers\StudentEnrollmentController;
use App\Http\Controllers\StudentTrackingController;


// =====================================================
// ROOT / WELCOME PAGE
// =====================================================

Route::get('/', function () {
    return view('welcome');
});


// =====================================================
// LOGIN
// =====================================================

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function (Request $request) {

    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (!Auth::attempt(
        $credentials,
        $request->boolean('remember')
    )) {
        return back()
            ->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])
            ->onlyInput('email');
    }

    $request->session()->regenerate();

    $role = strtolower(trim(Auth::user()->role ?? ''));

    // Infirmary
    if ($role === 'infirmary') {
        return redirect()->route('admin.step2');
    }

    // Admission
    if ($role === 'admission') {
        return redirect()->route('admin.step3');
    }

    // ID Capturing
    if ($role === 'id capturing') {
        return redirect()->route('admin.step4');
    }

    // GCO Staff
    if ($role === 'gco staff') {
        return redirect()->route('admin.step5');
    }

    // Super Admin
    if ($role === 'super admin') {
        return redirect()->route('admin.dashboard');
    }

    // DepEd Forms management roles
    if (in_array($role, [
        'admin',
        'admissions',
        'admissions_admin',
    ], true)) {
        return redirect()->route('admin.enrollment-forms');
    }

    // Unknown role
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()
        ->route('login')
        ->withErrors([
            'email' => 'This account does not have access.',
        ]);

})->name('login.submit');


// =====================================================
// LOGOUT
// =====================================================

Route::post('/logout', function (Request $request) {

    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');

})->middleware('auth')->name('logout');


// =====================================================
// PUBLIC STUDENT TRACKING
// =====================================================

Route::get(
    '/track',
    [StudentTrackingController::class, 'index']
)->name('tracking.form');

Route::post(
    '/track',
    [StudentTrackingController::class, 'search']
)->name('tracking.search');


// =====================================================
// ADMIN ROUTES
// =====================================================

Route::middleware(['auth'])
    ->prefix('admin')
    ->group(function () {

        // Super Admin Dashboard
        Route::middleware(['role:Super Admin'])->group(function () {

            Route::get(
                '/dashboard',
                [DashboardController::class, 'index']
            )->name('admin.dashboard');

        });


        // Step 1: Admission
        Route::middleware(['role:Admission,Super Admin'])->group(function () {

            Route::get(
                '/step-1',
                [DashboardController::class, 'step1']
            )->name('admin.step1');

        });


        // Step 2: Infirmary
        Route::middleware(['role:Infirmary,Super Admin'])->group(function () {

            Route::get(
                '/step2',
                [DashboardController::class, 'step2']
            )->name('admin.step2');

            Route::put(
                '/students/{id}',
                [DashboardController::class, 'updateStudent']
            )->name('admin.students.update');

            Route::post(
                '/students/{id}/advance-step3',
                [DashboardController::class, 'advanceToStep3']
            )->name('admin.students.advance-step3');

        });


        // Step 3: Admission
        Route::middleware(['role:Admission,Super Admin'])->group(function () {

            Route::get(
                '/step3',
                [DashboardController::class, 'step3']
            )->name('admin.step3');

            Route::post(
                '/students/{id}/advance-step4',
                [DashboardController::class, 'advanceToStep4']
            )->name('admin.students.advance-step4');

        });


        // Step 4: ID Capturing
        Route::middleware(['role:ID Capturing,Super Admin'])->group(function () {

            Route::get(
                '/step4',
                [DashboardController::class, 'step4']
            )->name('admin.step4');

            Route::post(
                '/students/{id}/advance-step5',
                [DashboardController::class, 'advanceToStep5']
            )->name('admin.students.advance-step5');

        });


        // Step 5: GCO Staff
        Route::middleware(['role:GCO Staff,Super Admin'])->group(function () {

            Route::get(
                '/step5',
                [DashboardController::class, 'step5']
            )->name('admin.step5');

            Route::post(
                '/students/{id}/advance-step6',
                [DashboardController::class, 'advanceToStep6']
            )->name('admin.students.advance-step6');

        });


        // Super Admin management routes
        Route::middleware(['role:Super Admin'])->group(function () {

            Route::get(
                '/step6',
                [DashboardController::class, 'step6']
            )->name('admin.step6');

            Route::get(
                '/capacity-control',
                [DashboardController::class, 'capacityControl']
            )->name('admin.capacity');

            Route::post(
                '/capacity-control/store',
                [DashboardController::class, 'storeSchedule']
            )->name('admin.capacity.store');

            Route::post(
                '/capacity-control/toggle/{id}',
                [DashboardController::class, 'toggleSchedule']
            )->name('admin.capacity.toggle');

            Route::post(
                '/capacity-control/bulk-update',
                [DashboardController::class, 'updateSchedules']
            )->name('admin.capacity.update');

            Route::get(
                '/accounts',
                [DashboardController::class, 'manageAccounts']
            )->name('admin.accounts');

            Route::post(
                '/accounts',
                [DashboardController::class, 'storeAccount']
            )->name('admin.accounts.store');

            Route::put(
                '/accounts/{id}',
                [DashboardController::class, 'updateAccount']
            )->name('admin.accounts.update');

            Route::post(
                '/students/{id}/advance',
                [DashboardController::class, 'advanceStudent']
            )->name('admin.students.advance');

        });

    });


// =====================================================
// DEPED FORMS MANAGEMENT + WORD TEMPLATE UPLOAD
// =====================================================

Route::middleware([
    'auth',
    'role:Super Admin,admin,admissions,admissions_admin',
])
    ->prefix('admin')
    ->group(function () {

        // Open PDF Field Mapping Editor
        Route::get(
            '/enrollment-forms/{form}/mapping',
            [DepedPdfMappingController::class, 'edit']
        )->name('admin.enrollment-forms.mapping');

        // Save PDF Field Mappings
        Route::post(
            '/enrollment-forms/{form}/mapping',
            [DepedPdfMappingController::class, 'save']
        )->name('admin.enrollment-forms.mapping.save');

        // Admin Word Template Management Routes
        Route::get('/deped-template', [AdminDepedFormController::class, 'index'])->name('admin.deped.template');
        Route::post('/deped-template/upload', [AdminDepedFormController::class, 'uploadTemplate'])->name('admin.deped.template.upload');


        use App\Http\Controllers\StudentDocumentController;

Route::middleware(['auth'])->group(function () {

    // Download template para sa estudyante
    Route::get(
        '/student/enrollment/word-template',
        [StudentDocumentController::class, 'downloadWordTemplate']
    )->name('student.enrollment.word-template');

    // Upload ng filled word form ng estudyante
    Route::post(
        '/student/enrollment/word-upload',
        [StudentDocumentController::class, 'uploadSubmission']
    )->name('student.enrollment.word-upload');

});
    });
