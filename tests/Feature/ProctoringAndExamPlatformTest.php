<?php

namespace Tests\Feature;

use App\Models\CmsPage;
use App\Models\EmailTemplate;
use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\FormSubmission;
use App\Models\ProctoringInfraction;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ProctoringAndExamPlatformTest extends TestCase
{
    protected User $admin;
    protected Exam $exam;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed core data
        $this->artisan('db:seed');

        $this->admin = User::where('email', 'admin@examcms.test')->first();
        $this->exam = Exam::where('course_code', 'CSC 455')->first();
    }

    public function test_homepage_and_cms_rules_pages_render_successfully(): void
    {
        $homeRes = $this->get('/');
        $homeRes->assertStatus(200);
        $homeRes->assertSee('CSC 455');
        $homeRes->assertSee('ProctorGuard');

        $rulesRes = $this->get('/page/rules');
        $rulesRes->assertStatus(200);
        $rulesRes->assertSee('Integrity');
    }

    public function test_public_contact_form_lead_submission(): void
    {
        $payload = [
            'form_name' => 'inquiry',
            'student_or_lead_name' => 'Jane Candidate',
            'email' => 'jane.candidate@example.com',
            'phone' => '+15551234567',
            'subject' => 'Special Accommodations',
            'message' => 'Requesting extended time accommodation.',
        ];

        $res = $this->post('/submit-lead', $payload);
        $res->assertSessionHas('success');

        $this->assertDatabaseHas('form_submissions', [
            'email' => 'jane.candidate@example.com',
            'status' => 'new',
        ]);
    }

    public function test_student_login_and_session_creation(): void
    {
        $entryRes = $this->get("/exam/{$this->exam->slug}");
        $entryRes->assertStatus(200);
        $entryRes->assertSee($this->exam->title);

        $loginPayload = [
            'student_id_code' => 'STU-2026-TEST1',
            'student_name' => 'John Doe',
            'student_email' => 'john.doe@test.com',
        ];

        $enterRes = $this->post("/exam/{$this->exam->slug}/enter", $loginPayload);
        $session = ExamSession::where('student_id_code', 'STU-2026-TEST1')->first();
        $this->assertNotNull($session);

        // Since exam is released, redirects to take
        $enterRes->assertRedirect(route('student.exam', $session->session_token));
    }

    public function test_canvas_data_endpoint_delivers_sanitized_questions_without_correct_keys(): void
    {
        $session = ExamSession::create([
            'exam_id' => $this->exam->id,
            'student_id_code' => 'STU-SEC-CHECK',
            'student_name' => 'Security Auditor',
            'student_email' => 'auditor@test.com',
            'status' => 'in_progress',
            'max_strikes' => 3,
            'question_order' => $this->exam->questions()->pluck('id')->toArray(),
            'started_at' => now(),
            'ends_at' => now()->addMinutes(45),
        ]);

        $res = $this->getJson("/api/proctor/canvas-data/{$session->session_token}");
        $res->assertStatus(200);
        $res->assertJsonPath('total_questions', 20);

        $data = $res->json();

        // Verify options contain text and id, but NEVER leak 'is_correct' field
        foreach ($data['questions'] as $q) {
            $this->assertNotEmpty($q['prompt']);
            foreach ($q['options'] as $opt) {
                $this->assertArrayHasKey('id', $opt);
                $this->assertArrayHasKey('text', $opt);
                $this->assertArrayNotHasKey('is_correct', $opt, 'CRITICAL SECURITY LEAK: is_correct must not be present in student canvas payload!');
            }
        }
    }

    public function test_heartbeat_synchronization(): void
    {
        $session = ExamSession::create([
            'exam_id' => $this->exam->id,
            'student_id_code' => 'STU-HB-TEST',
            'student_name' => 'Heartbeat Tester',
            'student_email' => 'hb@test.com',
            'status' => 'in_progress',
            'max_strikes' => 3,
            'started_at' => now(),
            'ends_at' => now()->addMinutes(45),
        ]);

        $res = $this->postJson("/api/proctor/heartbeat/{$session->session_token}", [
            'answers' => [1 => 2]
        ]);

        $res->assertStatus(200);
        $res->assertJsonPath('active', true);
        $res->assertJsonPath('status', 'in_progress');
        $this->assertGreaterThan(2600, $res->json('remaining_seconds'));
    }

    public function test_anti_cheat_infraction_recording_and_auto_disqualification_upon_max_strikes(): void
    {
        Mail::fake();

        $session = ExamSession::create([
            'exam_id' => $this->exam->id,
            'student_id_code' => 'STU-CHEAT-TEST',
            'student_name' => 'Cheating Candidate',
            'student_email' => 'cheater@test.com',
            'status' => 'in_progress',
            'max_strikes' => 3,
            'strikes_count' => 0,
            'started_at' => now(),
            'ends_at' => now()->addMinutes(45),
        ]);

        // Strike 1: Tab switch
        $res1 = $this->postJson("/api/proctor/infraction/{$session->session_token}", [
            'infraction_type' => 'visibility_hidden',
            'severity' => 'high',
            'details' => 'Tab switched away to Google Search',
        ]);
        $res1->assertStatus(200);
        $res1->assertJsonPath('strikes_count', 1);
        $res1->assertJsonPath('terminated', false);

        // Strike 2: Window blur
        $res2 = $this->postJson("/api/proctor/infraction/{$session->session_token}", [
            'infraction_type' => 'window_blur',
            'severity' => 'high',
            'details' => 'Window focus lost',
        ]);
        $res2->assertStatus(200);
        $res2->assertJsonPath('strikes_count', 2);
        $res2->assertJsonPath('terminated', false);

        // Strike 3: DevTools opened -> MUST TERMINATE AT ZERO SCORE
        $res3 = $this->postJson("/api/proctor/infraction/{$session->session_token}", [
            'infraction_type' => 'devtools_opened',
            'severity' => 'critical',
            'details' => 'DevTools console trap triggered',
        ]);
        $res3->assertStatus(200);
        $res3->assertJsonPath('strikes_count', 3);
        $res3->assertJsonPath('terminated', true);
        $this->assertStringContainsString('result', $res3->json('redirect_url'));

        // Verify session state in database
        $session->refresh();
        $this->assertEquals('terminated_cheat', $session->status);
        $this->assertEquals(0.00, (float)$session->score);
        $this->assertEquals(0.00, (float)$session->percentage);
        $this->assertFalse((bool)$session->passed);
        $this->assertNotNull($session->termination_reason);

        // Verify 3 infraction records exist
        $this->assertEquals(3, ProctoringInfraction::where('exam_session_id', $session->id)->count());
    }

    public function test_normal_exam_submission_and_automated_grading(): void
    {
        Mail::fake();

        $session = ExamSession::create([
            'exam_id' => $this->exam->id,
            'student_id_code' => 'STU-HONEST-99',
            'student_name' => 'Alice Honest',
            'student_email' => 'alice@test.com',
            'status' => 'in_progress',
            'max_strikes' => 3,
            'strikes_count' => 0,
            'started_at' => now(),
            'ends_at' => now()->addMinutes(45),
        ]);

        // Build answers: answer 18 out of 20 questions correctly (90%)
        $questions = $this->exam->questions()->with('options')->get();
        $answers = [];

        foreach ($questions as $idx => $q) {
            if ($idx < 18) {
                // Select correct option
                $correct = $q->options->firstWhere('is_correct', true);
                $answers[$q->id] = $correct->id;
            } else {
                // Select incorrect option
                $incorrect = $q->options->firstWhere('is_correct', false);
                $answers[$q->id] = $incorrect->id;
            }
        }

        $res = $this->post("/exam-session/{$session->session_token}/submit", [
            'answers' => $answers,
        ]);

        $res->assertRedirect(route('student.result', $session->session_token));

        $session->refresh();
        $this->assertEquals('completed', $session->status);
        $this->assertEquals(18.00, (float)$session->score);
        $this->assertEquals(20.00, (float)$session->max_score);
        $this->assertEquals(90.00, (float)$session->percentage);
        $this->assertTrue((bool)$session->passed);

        // Result view renders passed banner
        $resultView = $this->get("/exam-session/{$session->session_token}/result");
        $resultView->assertStatus(200);
        $resultView->assertSee('EXAMINATION PASSED');
        $resultView->assertSee('90.0%');
    }

    public function test_admin_authentication_and_cms_dashboard(): void
    {
        // Unauthenticated access redirects to login
        $this->get('/admin')->assertRedirect(route('admin.login'));

        // Login as admin
        $loginRes = $this->post('/admin/login', [
            'email' => 'admin@examcms.test',
            'password' => 'AdminSecret2026!',
        ]);
        $loginRes->assertRedirect(route('admin.dashboard'));

        $this->actingAs($this->admin);

        $dashRes = $this->get('/admin');
        $dashRes->assertStatus(200);
        $dashRes->assertSee('Integrity & Proctoring Overview');
        $dashRes->assertSee('CSC 455');
    }

    public function test_admin_live_proctoring_ledger_and_feed(): void
    {
        $this->actingAs($this->admin);

        $res = $this->get('/admin/proctoring');
        $res->assertStatus(200);
        $res->assertSee('Live Anti-Cheat Proctoring Ledger');

        $feedRes = $this->getJson('/admin/proctoring/feed');
        $feedRes->assertStatus(200);
        $feedRes->assertJsonStructure(['active_count', 'sessions']);
    }

    public function test_admin_dynamic_smtp_settings_update_and_test_email(): void
    {
        $this->actingAs($this->admin);

        $settingsRes = $this->get('/admin/settings');
        $settingsRes->assertStatus(200);
        $settingsRes->assertSee('Dynamic SMTP Email Configuration');

        // Update SMTP configuration using 'log' driver for local/test execution
        $updateRes = $this->post('/admin/settings', [
            'settings_group' => 'smtp',
            'mail_mailer' => 'log',
            'mail_host' => '127.0.0.1',
            'mail_port' => 2525,
            'mail_username' => 'exam_mailer',
            'mail_password' => 'mailer_secret_key',
            'mail_encryption' => 'tls',
            'mail_from_address' => 'proctoring-office@university.edu',
            'mail_from_name' => 'University Examination Board',
            'admin_alert_email' => 'chief-proctor@university.edu',
        ]);
        $updateRes->assertSessionHas('success');

        $this->assertEquals('127.0.0.1', SystemSetting::get('mail_host'));
        $this->assertEquals('proctoring-office@university.edu', SystemSetting::get('mail_from_address'));

        // Test email dispatch using log driver
        $testRes = $this->post('/admin/settings/test-email', [
            'test_email' => 'test-recipient@example.com',
        ]);
        $testRes->assertSessionHas('success');
    }
}
