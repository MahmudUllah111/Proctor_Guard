<?php

namespace App\Providers;

use App\Models\ExamSession;
use App\Models\FormSubmission;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer(['layouts.admin', 'layouts.student', 'admin.auth.login'], function ($view) {
            $view->with('siteName', SystemSetting::get('site_name', config('app.name')));
        });

        // Sidebar badges reflect live data instead of static labels.
        View::composer(['layouts.admin', 'admin.proctoring.index'], function ($view) {
            $view->with([
                'liveSessionCount' => ExamSession::whereIn('status', ['waiting_room', 'in_progress'])->count(),
                'newLeadCount' => FormSubmission::where('status', 'new')->count(),
            ]);
        });
    }
}
