<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CmsPageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmailTemplateController;
use App\Http\Controllers\Admin\ExamController;
use App\Http\Controllers\Admin\FormSubmissionController;
use App\Http\Controllers\Admin\MediaLibraryController;
use App\Http\Controllers\Admin\ProctoringLedgerController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Api\ProctoringApiController;
use App\Http\Controllers\Public\PageController;
use App\Http\Controllers\Student\ExamPortalController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public CMS & Website Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [PageController::class, 'home'])->name('home');
Route::post('/submit-lead', [PageController::class, 'submitLead'])->name('public.lead.submit');
Route::get('/page/{slug}', [PageController::class, 'show'])->name('public.page');

/*
|--------------------------------------------------------------------------
| Student Examination Portal Routes
|--------------------------------------------------------------------------
*/
Route::prefix('exam')->name('student.')->group(function () {
    Route::get('/{exam:slug}', [ExamPortalController::class, 'showEntry'])->name('entry');
    Route::post('/{exam:slug}/enter', [ExamPortalController::class, 'enter'])->name('enter');
});

Route::prefix('exam-session')->name('student.')->group(function () {
    Route::get('/{session}/waiting', [ExamPortalController::class, 'waitingRoom'])->name('waiting-room');
    Route::get('/{session}/take', [ExamPortalController::class, 'exam'])->name('exam');
    Route::post('/{session}/submit', [ExamPortalController::class, 'submit'])->name('submit');
    Route::get('/{session}/result', [ExamPortalController::class, 'result'])->name('result');
});

/*
|--------------------------------------------------------------------------
| Proctoring Engine API Routes (Canvas Data, Heartbeats, Infraction Logs)
|--------------------------------------------------------------------------
*/
Route::prefix('api/proctor')->name('api.proctor.')->group(function () {
    Route::get('/waiting-status/{token}', [ProctoringApiController::class, 'getWaitingStatus'])->name('waiting-status');
    Route::get('/canvas-data/{token}', [ProctoringApiController::class, 'getCanvasQuestionData'])->name('canvas-data');
    Route::post('/heartbeat/{token}', [ProctoringApiController::class, 'heartbeat'])->name('heartbeat');
    Route::post('/infraction/{token}', [ProctoringApiController::class, 'recordInfraction'])->name('infraction');
});

/*
|--------------------------------------------------------------------------
| Admin Authentication Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| Protected Admin CMS Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware('admin.auth')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Exam Builder
    Route::resource('exams', ExamController::class);
    Route::post('/exams/{exam}/toggle-release', [ExamController::class, 'toggleRelease'])->name('exams.toggle-release');
    Route::get('/exams/{exam}/report', [ExamController::class, 'report'])->name('exams.report');

    // Question Builder (Nested in Exams)
    Route::prefix('exams/{exam}')->name('exams.')->group(function () {
        Route::resource('questions', QuestionController::class)->except(['show']);
    });

    // Live Proctoring Ledger
    Route::prefix('proctoring')->name('proctoring.')->group(function () {
        Route::get('/', [ProctoringLedgerController::class, 'index'])->name('index');
        Route::get('/feed', [ProctoringLedgerController::class, 'feed'])->name('feed');
        Route::get('/{session}/infractions', [ProctoringLedgerController::class, 'getInfractions'])->name('infractions');
        Route::post('/{session}/terminate', [ProctoringLedgerController::class, 'terminate'])->name('terminate');
        Route::post('/{session}/reset-strikes', [ProctoringLedgerController::class, 'resetStrikes'])->name('reset-strikes');
    });

    // WordPress-Style Media Library
    Route::prefix('media')->name('media.')->group(function () {
        Route::get('/', [MediaLibraryController::class, 'index'])->name('index');
        Route::get('/items', [MediaLibraryController::class, 'items'])->name('items');
        Route::post('/upload', [MediaLibraryController::class, 'upload'])->name('upload');
        Route::delete('/{media}', [MediaLibraryController::class, 'destroy'])->name('destroy');
    });

    // Widget-Based Page Builder
    Route::resource('pages', CmsPageController::class);

    // Standalone Forms & Leads Manager
    Route::prefix('forms')->name('forms.')->group(function () {
        Route::get('/', [FormSubmissionController::class, 'index'])->name('index');
        Route::get('/export/csv', [FormSubmissionController::class, 'exportCsv'])->name('export');
        Route::get('/{submission}', [FormSubmissionController::class, 'show'])->name('show');
        Route::post('/{submission}/status', [FormSubmissionController::class, 'updateStatus'])->name('status');
        Route::delete('/{submission}', [FormSubmissionController::class, 'destroy'])->name('destroy');
    });

    // Settings (Dynamic SMTP, Global SEO, Proctoring Defaults)
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::post('/', [SettingsController::class, 'update'])->name('update');
        Route::post('/test-email', [SettingsController::class, 'testEmail'])->name('test-email');
    });

    // Email Templates
    Route::prefix('templates')->name('templates.')->group(function () {
        Route::get('/', [EmailTemplateController::class, 'index'])->name('index');
        Route::get('/{template}/edit', [EmailTemplateController::class, 'edit'])->name('edit');
        Route::put('/{template}', [EmailTemplateController::class, 'update'])->name('update');
        Route::get('/{template}/preview', [EmailTemplateController::class, 'preview'])->name('preview');
    });
});
