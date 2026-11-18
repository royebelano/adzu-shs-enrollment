<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\Admin\EnrollmentAdminController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AccessController;
use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\CapacityController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\Step2Controller;
use App\Http\Controllers\Admin\StudentDirectoryController;
use App\Http\Controllers\Admin\WorkflowController;
use App\Http\Controllers\StudentDocumentController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

// HOME
Route::get('/', function () {
    return view('welcome');
})->name('home');


/*
|--------------------------------------------------------------------------
| LOGIN REDIRECT
|--------------------------------------------------------------------------
*/

Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');


/*
|--------------------------------------------------------------------------
| STUDENT ENROLLMENT ROUTES
|--------------------------------------------------------------------------
| Public access - no authentication required.
*/

// DISPLAY ENROLLMENT FORM
Route::get('/enrollment/form', [EnrollmentController::class, 'create'])
    ->name('enrollment.create');

// ADMISSION APPOINTMENT SLOTS (used by the booking calendar)
Route::get('/enrollment/slots', [EnrollmentController::class, 'slots'])
    ->middleware('throttle:60,1')
    ->name('enrollment.slots');

// TRACK APPLICATION (Reference ID + email)
Route::get('/enrollment/track', [EnrollmentController::class, 'track'])
    ->name('enrollment.track');

Route::post('/enrollment/track', [EnrollmentController::class, 'trackCheck'])
    ->middleware('throttle:10,1')
    ->name('enrollment.track.check');

// SUBMIT ENROLLMENT FORM
Route::post('/enrollment/submit', [EnrollmentController::class, 'store'])
    ->name('enrollment.store');


/*
|--------------------------------------------------------------------------
| ADMIN LOGIN ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    // ADMIN LOGIN PAGE
    Route::get('/admin/login', function () {
        return view('admin.login');
    })->name('admin.login');


    // PROCESS ADMIN LOGIN
    Route::post('/admin/login', function (Request $request) {

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Disabled accounts cannot sign in.
        if (Auth::attempt(
            $credentials + ['is_active' => true],
            $request->boolean('remember')
        )) {

            $request->session()->regenerate();

            $user = $request->user();
            $user->forceFill(['last_login_at' => now()])->save();

            // Each office lands on the first page it is allowed to open.
            return redirect()->intended($user->homeUrl());
        }

        return back()
            ->withErrors([
                'email' => 'Invalid email or password, or the account is disabled.',
            ])
            ->onlyInput('email');

    })->name('admin.login.submit');

});


/*
|--------------------------------------------------------------------------
| AUTHENTICATED ADMIN ROUTES
|--------------------------------------------------------------------------
| All routes below require admin authentication.
*/

Route::middleware(['auth', 'active'])->group(function () {


    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    */

    Route::post('/admin/logout', function (Request $request) {

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');

    })->name('admin.logout');


    /*
    |--------------------------------------------------------------------------
    | ADMIN DASHBOARD
    |--------------------------------------------------------------------------
    */

    Route::get('/admin/dashboard', [DashboardController::class, 'index'])
        ->middleware('access:dashboard')->name('admin.dashboard');


    /*
    |--------------------------------------------------------------------------
    | ENROLLMENT LIST
    |--------------------------------------------------------------------------
    */

    Route::get('/admin', [EnrollmentAdminController::class, 'index'])
        ->middleware('access:step1')->name('admin.index');


    /*
    |--------------------------------------------------------------------------
    | EXPORT ENROLLMENTS
    |--------------------------------------------------------------------------
    */

    Route::get('/admin/export', [EnrollmentAdminController::class, 'export'])
        ->middleware('access:step1')->name('admin.export');


    /*
    |--------------------------------------------------------------------------
    | STEP 1 - STUDENT DETAILS
    |--------------------------------------------------------------------------
    | View:
    | resources/views/admin/steps/step1/step1-view.blade.php
    */

    Route::get(
        '/admin/steps/step1/{enrollment}',
        [EnrollmentAdminController::class, 'show']
    )->middleware('access:step1')->name('admin.step1.view');


    /*
    |--------------------------------------------------------------------------
    | STEP 2 - INFIRMARY
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/admin/enrollments/{enrollment}/proceed-step2',
        [Step2Controller::class, 'proceed']
    )->middleware('access:step1,edit')->name('admin.step2.proceed');

    Route::get('/admin/step2', [Step2Controller::class, 'index'])
        ->middleware('access:step2')->name('admin.step2.index');

    Route::get(
        '/admin/steps/step2/{enrollment}',
        [Step2Controller::class, 'show']
    )->middleware('access:step2')->name('admin.step2.view');

    Route::get(
        '/admin/steps/step2/{enrollment}/print',
        [Step2Controller::class, 'print']
    )->middleware('access:step2')->name('admin.step2.print');


    /*
    |--------------------------------------------------------------------------
    | STEPS 3 TO 6 - ADMISSION, ID CAPTURING, GCO ASSESSMENT, ENROLLED
    |--------------------------------------------------------------------------
    */

    // Reports
    Route::get('/admin/reports', [ReportController::class, 'index'])->middleware('access:reports')->name('admin.reports.index');

    // Student Directory
    Route::get('/admin/students', [StudentDirectoryController::class, 'index'])->middleware('access:students')->name('admin.students.index');
    Route::get('/admin/students/export', [StudentDirectoryController::class, 'export'])->middleware('access:students')->name('admin.students.export');

    // ---------------------------------------------------------------
    // System pages (Administrator only)
    // ---------------------------------------------------------------

    // Manage Account
    Route::get('/admin/accounts', [AccountController::class, 'index'])->middleware('access:system')->name('admin.accounts.index');
    Route::post('/admin/accounts', [AccountController::class, 'store'])->middleware('access:system')->name('admin.accounts.store');
    Route::get('/admin/accounts/{user}/edit', [AccountController::class, 'edit'])->middleware('access:system')->name('admin.accounts.edit');
    Route::put('/admin/accounts/{user}', [AccountController::class, 'update'])->middleware('access:system')->name('admin.accounts.update');
    Route::put('/admin/accounts/{user}/password', [AccountController::class, 'password'])->middleware('access:system')->name('admin.accounts.password');
    Route::post('/admin/accounts/{user}/toggle', [AccountController::class, 'toggle'])->middleware('access:system')->name('admin.accounts.toggle');
    Route::delete('/admin/accounts/{user}', [AccountController::class, 'destroy'])->middleware('access:system')->name('admin.accounts.destroy');

    // Access Rights
    Route::get('/admin/access', [AccessController::class, 'index'])->middleware('access:system')->name('admin.access.index');
    Route::put('/admin/access', [AccessController::class, 'update'])->middleware('access:system')->name('admin.access.update');
    Route::post('/admin/access/reset', [AccessController::class, 'reset'])->middleware('access:system')->name('admin.access.reset');

    // Settings
    Route::get('/admin/settings', [SettingsController::class, 'index'])->middleware('access:system')->name('admin.settings.index');
    Route::put('/admin/settings', [SettingsController::class, 'update'])->middleware('access:system')->name('admin.settings.update');

    // My account (every signed-in person)
    Route::get('/admin/profile', [ProfileController::class, 'index'])->name('admin.profile');
    Route::put('/admin/profile', [ProfileController::class, 'update'])->name('admin.profile.update');
    Route::put('/admin/profile/password', [ProfileController::class, 'password'])->name('admin.profile.password');

    // Capacity Control (admission schedule, set date by date)
    Route::get('/admin/capacity', [CapacityController::class, 'index'])->middleware('access:capacity')->name('admin.capacity.index');
    Route::post('/admin/capacity/dates', [CapacityController::class, 'addDate'])->middleware('access:capacity')->name('admin.capacity.add');
    Route::post('/admin/capacity/hours', [CapacityController::class, 'addHour'])->middleware('access:capacity')->name('admin.capacity.hour');
    Route::put('/admin/capacity', [CapacityController::class, 'save'])->middleware('access:capacity')->name('admin.capacity.save');
    Route::delete('/admin/capacity/date', [CapacityController::class, 'removeDate'])->middleware('access:capacity')->name('admin.capacity.remove');

    // Walk-in enrollment, filled in by admission staff
    Route::get('/admin/walk-in', [EnrollmentController::class, 'walkIn'])->middleware('access:step1,edit')->name('admin.walkin.create');
    Route::post('/admin/walk-in', [EnrollmentController::class, 'storeWalkIn'])->middleware('access:step1,edit')->name('admin.walkin.store');

    // Step 3 - Admission
    Route::get('/admin/step3', [WorkflowController::class, 'step3Index'])->middleware('access:step3')->name('admin.step3.index');
    Route::get('/admin/steps/step3/{enrollment}', [WorkflowController::class, 'step3Show'])->middleware('access:step3')->name('admin.step3.view');
    Route::put('/admin/steps/step3/{enrollment}', [WorkflowController::class, 'step3Save'])->middleware('access:step3')->name('admin.step3.save');
    Route::post('/admin/enrollments/{enrollment}/proceed-step3', [WorkflowController::class, 'proceedStep3'])->middleware('access:step2,edit')->name('admin.step3.proceed');

    // Step 4 - ID Capturing
    Route::get('/admin/step4', [WorkflowController::class, 'step4Index'])->middleware('access:step4')->name('admin.step4.index');
    Route::get('/admin/steps/step4/{enrollment}', [WorkflowController::class, 'step4Show'])->middleware('access:step4')->name('admin.step4.view');
    Route::put('/admin/steps/step4/{enrollment}', [WorkflowController::class, 'step4Save'])->middleware('access:step4')->name('admin.step4.save');
    Route::post('/admin/enrollments/{enrollment}/proceed-step4', [WorkflowController::class, 'proceedStep4'])->middleware('access:step3,edit')->name('admin.step4.proceed');

    // Step 5 - GCO Assessment
    Route::get('/admin/step5', [WorkflowController::class, 'step5Index'])->middleware('access:step5')->name('admin.step5.index');
    Route::get('/admin/steps/step5/{enrollment}', [WorkflowController::class, 'step5Show'])->middleware('access:step5')->name('admin.step5.view');
    Route::put('/admin/steps/step5/{enrollment}', [WorkflowController::class, 'step5Save'])->middleware('access:step5')->name('admin.step5.save');
    Route::post('/admin/enrollments/{enrollment}/proceed-step5', [WorkflowController::class, 'proceedStep5'])->middleware('access:step4,edit')->name('admin.step5.proceed');

    // Step 6 - Enrolled
    Route::post('/admin/enrollments/{enrollment}/proceed-step6', [WorkflowController::class, 'proceedStep6'])->middleware('access:step5,edit')->name('admin.step6.proceed');
    Route::get('/admin/step6', [WorkflowController::class, 'step6Index'])->middleware('access:step6')->name('admin.step6.index');
    Route::get('/admin/steps/step6/{enrollment}', [WorkflowController::class, 'step6Show'])->middleware('access:step6')->name('admin.step6.view');


    /*
    |--------------------------------------------------------------------------
    | VIEW / DOWNLOAD ENROLLMENT PDF
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admin/enrollments/{enrollment}/pdf',
        [EnrollmentAdminController::class, 'pdf']
    )->middleware('access:step1|step3')->name('admin.pdf');


    /*
    |--------------------------------------------------------------------------
    | DOWNLOAD COMPLETE ENROLLMENT ZIP
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admin/enrollments/{enrollment}/zip',
        [EnrollmentAdminController::class, 'zip']
    )->middleware('access:step3')->name('admin.zip');


    /*
    |--------------------------------------------------------------------------
    | VIEW / DOWNLOAD STUDENT DOCUMENT
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admin/enrollments/{enrollment}/documents/{type}',
        [EnrollmentAdminController::class, 'document']
    )->middleware('access:step3')->name('admin.document');


    /*
    |--------------------------------------------------------------------------
    | UPDATE ENROLLMENT STATUS
    |--------------------------------------------------------------------------
    */

    Route::patch(
        '/admin/enrollments/{enrollment}/status',
        [EnrollmentAdminController::class, 'updateStatus']
    )->middleware('access:step1,edit')->name('admin.status');


    /*
    |--------------------------------------------------------------------------
    | DELETE ENROLLMENT
    |--------------------------------------------------------------------------
    */

    Route::delete(
        '/admin/enrollments/{enrollment}',
        [EnrollmentAdminController::class, 'destroy']
    )->middleware('access:system')->name('admin.destroy');

});
