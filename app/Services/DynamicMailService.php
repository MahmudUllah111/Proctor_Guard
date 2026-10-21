<?php

namespace App\Services;

use App\Models\EmailTemplate;
use App\Models\ExamSession;
use App\Models\ProctoringInfraction;
use App\Models\SystemSetting;
use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class DynamicMailService
{
    /**
     * Configure the mail transport dynamically using database system settings.
     */
    public function configureMailer(): void
    {
        $mailer = SystemSetting::get('mail_mailer', config('mail.default', 'smtp'));
        $host = SystemSetting::get('mail_host', config('mail.mailers.smtp.host', '127.0.0.1'));
        $port = SystemSetting::get('mail_port', config('mail.mailers.smtp.port', 2525));
        $username = SystemSetting::get('mail_username', config('mail.mailers.smtp.username'));
        $password = SystemSetting::get('mail_password', config('mail.mailers.smtp.password'));
        $encryption = SystemSetting::get('mail_encryption', config('mail.mailers.smtp.encryption', 'tls'));
        $fromAddress = SystemSetting::get('mail_from_address', config('mail.from.address', 'proctor@examcms.test'));
        $fromName = SystemSetting::get('mail_from_name', config('mail.from.name', 'Exam Proctoring System'));

        // If encryption is empty or 'none', pass null
        if (in_array(strtolower((string)$encryption), ['', 'none', 'null'])) {
            $encryption = null;
        }

        Config::set('mail.default', $mailer);
        Config::set('mail.mailers.smtp.host', $host);
        Config::set('mail.mailers.smtp.port', (int) $port);
        Config::set('mail.mailers.smtp.username', $username);
        Config::set('mail.mailers.smtp.password', $password);
        Config::set('mail.mailers.smtp.encryption', $encryption);
        Config::set('mail.from.address', $fromAddress);
        Config::set('mail.from.name', $fromName);
    }

    /**
     * Send student score report email using dynamic template.
     */
    public function sendStudentScoreReport(ExamSession $session): array
    {
        try {
            $this->configureMailer();

            $template = EmailTemplate::where('template_key', 'student_score_report')
                ->where('is_active', true)
                ->first();

            $passedText = $session->passed ? 'PASSED' : 'NOT PASSED';
            $statusBadge = $session->passed
                ? '<span style="display:inline-block;padding:6px 16px;background:#10b981;color:#ffffff;border-radius:9999px;font-weight:bold;font-size:14px;">PASSED</span>'
                : '<span style="display:inline-block;padding:6px 16px;background:#ef4444;color:#ffffff;border-radius:9999px;font-weight:bold;font-size:14px;">NOT PASSED</span>';

            $data = [
                'student_name' => $session->student_name,
                'student_id' => $session->student_id_code,
                'exam_title' => $session->exam->title ?? 'Examination',
                'course_code' => $session->exam->course_code ?? 'N/A',
                'score' => number_format((float)$session->score, 1),
                'max_score' => number_format((float)$session->max_score, 1),
                'percentage' => number_format((float)$session->percentage, 1),
                'pass_percentage' => number_format((float)($session->exam->pass_percentage ?? 70), 1),
                'status_text' => $passedText,
                'status_badge' => $statusBadge,
                'submission_date' => $session->submitted_at ? $session->submitted_at->toDayDateTimeString() : now()->toDayDateTimeString(),
                'ip_address' => $session->ip_address ?? 'N/A',
                'strikes_count' => (string)$session->strikes_count,
            ];

            $rendered = $template ? $template->render($data) : [
                'subject' => "Exam Score Report: {$data['exam_title']}",
                'body_html' => "<h2>Hello {$data['student_name']},</h2><p>Your examination result for <strong>{$data['exam_title']}</strong> is: {$data['score']}/{$data['max_score']} ({$data['percentage']}%). Status: {$data['status_text']}.</p>",
            ];

            Mail::html($rendered['body_html'], function ($message) use ($session, $rendered) {
                $message->to($session->student_email, $session->student_name)
                    ->subject($rendered['subject']);
            });

            $session->update(['email_report_dispatched' => true]);

            return ['success' => true];
        } catch (Exception $e) {
            Log::error('Failed to send student score report email: ' . $e->getMessage(), [
                'session_id' => $session->id,
                'error' => $e->getMessage(),
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Send security strike alert email to administrator.
     */
    public function sendAdminStrikeAlert(ExamSession $session, ProctoringInfraction $infraction): array
    {
        try {
            $this->configureMailer();

            $adminEmail = SystemSetting::get('admin_alert_email', config('mail.from.address', 'admin@examcms.test'));
            if (empty($adminEmail)) {
                $adminEmail = 'admin@examcms.test';
            }

            $template = EmailTemplate::where('template_key', 'admin_strike_alert')
                ->where('is_active', true)
                ->first();

            $data = [
                'student_name' => $session->student_name,
                'student_id' => $session->student_id_code,
                'student_email' => $session->student_email,
                'exam_title' => $session->exam->title ?? 'Examination',
                'course_code' => $session->exam->course_code ?? 'N/A',
                'infraction_type' => $infraction->formatted_type,
                'severity' => strtoupper($infraction->severity),
                'strike_number' => (string)$infraction->strike_number,
                'max_strikes' => (string)$session->max_strikes,
                'status' => strtoupper($session->status),
                'details' => $infraction->details ?? 'N/A',
                'ip_address' => $infraction->ip_address ?? $session->ip_address ?? 'N/A',
                'timestamp' => now()->toDayDateTimeString(),
            ];

            $rendered = $template ? $template->render($data) : [
                'subject' => "[SECURITY ALERT] Cheat Infraction Strike {$data['strike_number']}/{$data['max_strikes']}: {$data['student_name']} ({$data['exam_title']})",
                'body_html' => "<h2>Security Proctoring Alert</h2><p>Candidate <strong>{$data['student_name']}</strong> ({$data['student_id']}) committed infraction: <strong>{$data['infraction_type']}</strong>.</p><p>Strike: {$data['strike_number']}/{$data['max_strikes']}. Session Status: {$data['status']}. Details: {$data['details']}.</p>",
            ];

            Mail::html($rendered['body_html'], function ($message) use ($adminEmail, $rendered) {
                $message->to($adminEmail)
                    ->subject($rendered['subject']);
            });

            return ['success' => true];
        } catch (Exception $e) {
            Log::error('Failed to send admin strike alert email: ' . $e->getMessage(), [
                'session_id' => $session->id,
                'error' => $e->getMessage(),
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Send test email to verify SMTP configuration.
     */
    public function sendTestEmail(string $recipientEmail): array
    {
        try {
            $this->configureMailer();

            $body = '
            <div style="font-family:sans-serif;padding:30px;background:#f8fafc;color:#1e293b;border-radius:12px;border:1px solid #e2e8f0;max-width:600px;margin:0 auto;">
                <h2 style="color:#2563eb;margin-top:0;">SMTP Configuration Test Successful</h2>
                <p>This is a verification email dispatched by your <strong>Quiz & Exam Proctoring CMS Platform</strong>.</p>
                <div style="background:#ffffff;padding:15px;border-radius:8px;border:1px solid #cbd5e1;margin:20px 0;">
                    <p style="margin:4px 0;"><strong>Timestamp:</strong> ' . now()->toRfc850String() . '</p>
                    <p style="margin:4px 0;"><strong>Active Mailer:</strong> ' . config('mail.default') . '</p>
                    <p style="margin:4px 0;"><strong>SMTP Host:</strong> ' . config('mail.mailers.smtp.host') . '</p>
                    <p style="margin:4px 0;"><strong>SMTP Port:</strong> ' . config('mail.mailers.smtp.port') . '</p>
                </div>
                <p style="font-size:12px;color:#64748b;">All system notifications, student score reports, and anti-cheat alerts are operational.</p>
            </div>';

            Mail::html($body, function ($message) use ($recipientEmail) {
                $message->to($recipientEmail)
                    ->subject('SMTP Verification: Proctoring Engine Ready');
            });

            return ['success' => true, 'message' => 'Test email dispatched successfully to ' . $recipientEmail];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
