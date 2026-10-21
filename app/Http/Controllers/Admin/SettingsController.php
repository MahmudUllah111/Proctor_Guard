<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Services\DynamicMailService;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function __construct(
        protected DynamicMailService $mailService
    ) {}

    public function index()
    {
        $settings = [
            // SMTP
            'mail_mailer' => SystemSetting::get('mail_mailer', 'smtp'),
            'mail_host' => SystemSetting::get('mail_host', '127.0.0.1'),
            'mail_port' => SystemSetting::get('mail_port', '2525'),
            'mail_username' => SystemSetting::get('mail_username', ''),
            'mail_password' => SystemSetting::get('mail_password', ''),
            'mail_encryption' => SystemSetting::get('mail_encryption', 'tls'),
            'mail_from_address' => SystemSetting::get('mail_from_address', 'proctor@examcms.test'),
            'mail_from_name' => SystemSetting::get('mail_from_name', 'Exam Proctoring System'),
            'admin_alert_email' => SystemSetting::get('admin_alert_email', 'admin@examcms.test'),

            // Global SEO
            'site_name' => SystemSetting::get('site_name', 'ProctorGuard Exam CMS'),
            'seo_title_default' => SystemSetting::get('seo_title_default', 'ProctorGuard - Enterprise Quiz & Proctoring CMS'),
            'seo_description_default' => SystemSetting::get('seo_description_default', 'Zero-leakage, proctored online examinations with canvas pixel buffer rendering and forensic watermarking.'),
            'seo_keywords_default' => SystemSetting::get('seo_keywords_default', 'proctoring, online exam, anti-cheat, canvas proctor, quiz cms'),
            'seo_og_image' => SystemSetting::get('seo_og_image', ''),
            'seo_robots' => SystemSetting::get('seo_robots', 'index, follow'),

            // Anti-Cheat Defaults
            'default_max_strikes' => SystemSetting::get('default_max_strikes', 3),
            'default_strictness' => SystemSetting::get('default_strictness', 'strict'),
            'watermark_opacity' => SystemSetting::get('watermark_opacity', '0.12'),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $group = $request->input('settings_group', 'smtp');

        if ($group === 'smtp') {
            $validated = $request->validate([
                'mail_mailer' => 'required|string',
                'mail_host' => 'required|string',
                'mail_port' => 'required|integer',
                'mail_username' => 'nullable|string',
                'mail_password' => 'nullable|string',
                'mail_encryption' => 'nullable|string',
                'mail_from_address' => 'required|email',
                'mail_from_name' => 'required|string',
                'admin_alert_email' => 'required|email',
            ]);

            foreach ($validated as $key => $val) {
                $isSecret = in_array($key, ['mail_password']);
                SystemSetting::set($key, $val, 'smtp', 'string', $isSecret);
            }

            return back()->with('success', 'Dynamic SMTP configuration updated successfully.');
        }

        if ($group === 'seo') {
            $validated = $request->validate([
                'site_name' => 'required|string|max:100',
                'seo_title_default' => 'required|string|max:200',
                'seo_description_default' => 'required|string|max:500',
                'seo_keywords_default' => 'nullable|string|max:255',
                'seo_og_image' => 'nullable|string|max:500',
                'seo_robots' => 'required|string|max:50',
            ]);

            foreach ($validated as $key => $val) {
                SystemSetting::set($key, $val, 'seo', 'string');
            }

            return back()->with('success', 'Global SEO parameters updated.');
        }

        if ($group === 'proctoring') {
            $validated = $request->validate([
                'default_max_strikes' => 'required|integer|min:1|max:20',
                'default_strictness' => 'required|in:strict,medium,lenient',
                'watermark_opacity' => 'required|numeric|min:0.01|max:1.0',
            ]);

            foreach ($validated as $key => $val) {
                SystemSetting::set($key, $val, 'proctoring', is_numeric($val) ? 'string' : 'string');
            }

            return back()->with('success', 'Proctoring engine defaults updated.');
        }

        return back();
    }

    public function testEmail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
        ]);

        $recipient = $request->input('test_email');
        $result = $this->mailService->sendTestEmail($recipient);

        if ($result['success']) {
            return back()->with('success', "Test email sent successfully to {$recipient}!");
        }

        return back()->withErrors(['test_email' => 'Failed to dispatch test email: ' . $result['error']]);
    }
}
