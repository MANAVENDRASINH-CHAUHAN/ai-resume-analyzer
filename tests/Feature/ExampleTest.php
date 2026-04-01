<?php

namespace Tests\Feature;

use App\Models\AppNotification;
use App\Models\JobRole;
use App\Models\Resume;
use App\Models\User;
use App\Services\ResumeScorerService;
use App\Services\SkillMatcherService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_login_and_register_pages_load_successfully(): void
    {
        $this->seed();

        $this->get('/')->assertStatus(200)->assertSee('AI Resume Analyzer System');
        $this->get('/login')->assertStatus(200)->assertSee('Login');
        $this->get('/register')->assertStatus(200)->assertSee('Candidate Registration');
        $this->get('/register/admin')->assertStatus(200)->assertSee('Admin Registration');
    }

    public function test_candidate_can_register_and_is_redirected_to_candidate_dashboard(): void
    {
        $response = $this->post('/register', [
            'name' => 'New Candidate',
            'email' => 'newcandidate@example.com',
            'phone' => '9876500000',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect('/user/dashboard');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'newcandidate@example.com',
            'role' => 'candidate',
        ]);
    }

    public function test_admin_login_redirects_to_admin_dashboard(): void
    {
        $this->seed();

        $response = $this->post('/login', [
            'email' => 'admin@resumeanalyzer.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticated();
    }

    public function test_admin_can_register_and_is_redirected_to_admin_dashboard(): void
    {
        $response = $this->post('/register/admin', [
            'name' => 'New Admin',
            'email' => 'newadmin@example.com',
            'phone' => '9999999999',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'newadmin@example.com',
            'role' => 'admin',
        ]);
    }

    public function test_candidate_cannot_access_admin_dashboard(): void
    {
        $this->seed();

        $candidate = User::where('role', 'candidate')->firstOrFail();
        $this->actingAs($candidate);

        $this->get('/admin/dashboard')
            ->assertRedirect('/user/dashboard');
    }

    public function test_guest_cannot_access_candidate_dashboard(): void
    {
        $this->get('/user/dashboard')->assertRedirect('/login');
    }

    public function test_candidate_can_open_dashboard_profile_and_placeholder_pages(): void
    {
        $this->seed();

        $candidate = User::where('role', 'candidate')->firstOrFail();
        $this->actingAs($candidate);

        $this->get('/user/dashboard')
            ->assertOk()
            ->assertSee($candidate->name)
            ->assertSee('Quick Actions');

        $this->get('/user/profile')
            ->assertOk()
            ->assertSee('Profile');

        $this->get('/user/resumes/upload')
            ->assertOk()
            ->assertSee('Upload Resume');
    }

    public function test_admin_can_open_dashboard_and_sidebar_pages(): void
    {
        $this->seed();

        $admin = User::where('role', 'admin')->firstOrFail();
        $this->actingAs($admin);

        $this->get('/admin/dashboard')
            ->assertOk()
            ->assertSee('Admin Dashboard')
            ->assertSee('Recent Resumes');

        $this->get('/admin/job-roles')
            ->assertOk()
            ->assertSee('Manage Job Roles');

        $this->get('/admin/users')->assertNotFound();
        $this->get('/admin/activity-logs')->assertNotFound();
    }

    public function test_candidate_can_upload_view_and_delete_resume(): void
    {
        Storage::fake('public');
        $this->seed();

        $candidate = User::where('role', 'candidate')->firstOrFail();
        $jobRole = JobRole::firstOrFail();

        $this->actingAs($candidate);

        $response = $this->post('/user/resumes', [
            'job_role_id' => $jobRole->id,
            'resume_file' => UploadedFile::fake()->create('candidate-resume.pdf', 300, 'application/pdf'),
        ]);

        $resume = Resume::firstOrFail();

        $response->assertRedirect('/user/resumes/' . $resume->id);
        $this->assertDatabaseHas('resumes', [
            'id' => $resume->id,
            'user_id' => $candidate->id,
            'job_role_id' => $jobRole->id,
            'upload_status' => 'uploaded',
            'analysis_status' => 'pending',
            'progress_percent' => 0,
        ]);

        Storage::disk('public')->assertExists($resume->file_path);

        $this->get('/user/resumes')
            ->assertOk()
            ->assertSee('My Resume History')
            ->assertSee($resume->file_name);

        $this->get('/user/resumes/' . $resume->id)
            ->assertOk()
            ->assertSee('Resume Details')
            ->assertSee($resume->file_name);

        $this->delete('/user/resumes/' . $resume->id)
            ->assertRedirect('/user/resumes');

        Storage::disk('public')->assertMissing($resume->file_path);
        $this->assertSoftDeleted('resumes', ['id' => $resume->id]);
    }

    public function test_candidate_cannot_open_another_users_resume(): void
    {
        Storage::fake('public');
        $this->seed();

        $owner = User::where('role', 'candidate')->firstOrFail();
        $anotherCandidate = User::factory()->create([
            'role' => 'candidate',
            'status' => 'active',
        ]);
        $jobRole = JobRole::firstOrFail();

        $this->actingAs($owner);
        $this->post('/user/resumes', [
            'job_role_id' => $jobRole->id,
            'resume_file' => UploadedFile::fake()->create('owner-resume.docx', 250, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'),
        ]);

        $resume = Resume::firstOrFail();

        $this->actingAs($anotherCandidate);

        $this->get('/user/resumes/' . $resume->id)->assertNotFound();
    }

    public function test_candidate_can_analyze_resume_and_save_analysis_data(): void
    {
        Storage::fake('public');
        $this->seed();

        $candidate = User::where('role', 'candidate')->firstOrFail();
        $jobRole = JobRole::where('title', 'Laravel Developer')->firstOrFail();

        $this->actingAs($candidate);

        $this->post('/user/resumes', [
            'job_role_id' => $jobRole->id,
            'resume_file' => UploadedFile::fake()->create('analysis-resume.pdf', 300, 'application/pdf'),
        ]);

        $resume = Resume::firstOrFail();

        $analysisText = <<<TEXT
        John Doe
        john.doe@example.com
        +91 9876543210
        Bengaluru, India

        Summary
        Laravel developer with backend experience and API integration knowledge.

        Skills
        PHP
        Laravel
        MySQL
        API
        Git

        Education
        Bachelor of Computer Applications from ABC College.

        Experience
        Worked on Laravel applications, MySQL database design, and REST API development.

        Projects
        Built a student portal and resume management system using PHP and Laravel.

        Certifications
        Completed web development certification.
        TEXT;

        $response = $this->post('/user/resumes/' . $resume->id . '/analyze', [
            'manual_text' => $analysisText,
        ]);

        $response->assertRedirect('/user/resumes/' . $resume->id);

        $this->assertDatabaseHas('resumes', [
            'id' => $resume->id,
            'upload_status' => 'analyzed',
            'analysis_status' => 'completed',
            'progress_percent' => 100,
        ]);

        $this->assertDatabaseHas('extracted_resume_data', [
            'resume_id' => $resume->id,
            'full_name' => 'John Doe',
            'email' => 'john.doe@example.com',
        ]);

        $this->assertDatabaseHas('resume_scores', [
            'resume_id' => $resume->id,
        ]);

        $this->assertDatabaseHas('analysis_reports', [
            'resume_id' => $resume->id,
        ]);

        $this->assertDatabaseHas('resume_skill_map', [
            'resume_id' => $resume->id,
            'matched_type' => 'matched',
        ]);

        $this->get('/user/resumes/' . $resume->id)
            ->assertOk()
            ->assertSee('Extracted Contact Details')
            ->assertSee('John Doe')
            ->assertSee('Detected Skills');

        $this->get('/user/reports')
            ->assertOk()
            ->assertSee('Resume Reports')
            ->assertSee($resume->file_name);

        $this->get('/user/reports/' . $resume->id)
            ->assertOk()
            ->assertSee('Analysis Report')
            ->assertSee('John Doe')
            ->assertSee('Detected Skills');

        $this->get('/user/reports/' . $resume->id . '/print')
            ->assertOk()
            ->assertSee('Printable Analysis Report');
    }

    public function test_ajax_resume_status_polling_completes_analysis_and_returns_json(): void
    {
        Storage::fake('public');
        $this->seed();

        $candidate = User::where('role', 'candidate')->firstOrFail();
        $jobRole = JobRole::where('title', 'Laravel Developer')->firstOrFail();

        $this->actingAs($candidate);

        $this->post('/user/resumes', [
            'job_role_id' => $jobRole->id,
            'resume_file' => UploadedFile::fake()->create('live-analysis.pdf', 300, 'application/pdf'),
        ]);

        $resume = Resume::firstOrFail();

        $analysisText = <<<TEXT
        John Doe
        john.doe@example.com
        +91 9876543210
        Bengaluru, India

        Summary
        Laravel developer with backend experience and API integration knowledge.

        Skills
        PHP
        Laravel
        MySQL
        API
        Git

        Education
        Bachelor of Computer Applications from ABC College.

        Experience
        Worked on Laravel applications, MySQL database design, and REST API development.

        Projects
        Built a student portal and resume management system using PHP and Laravel.
        TEXT;

        $this->postJson('/user/resumes/' . $resume->id . '/analyze', [
            'manual_text' => $analysisText,
        ])->assertOk()
            ->assertJsonPath('resume.analysis_status', 'in_progress')
            ->assertJsonPath('resume.progress_percent', 25);

        $this->getJson('/user/resumes/' . $resume->id . '/status')
            ->assertOk()
            ->assertJsonPath('analysis_status', 'in_progress')
            ->assertJsonPath('progress_percent', 50);

        $this->getJson('/user/resumes/' . $resume->id . '/status')
            ->assertOk()
            ->assertJsonPath('analysis_status', 'in_progress')
            ->assertJsonPath('progress_percent', 75);

        $this->getJson('/user/resumes/' . $resume->id . '/status')
            ->assertOk()
            ->assertJsonPath('analysis_status', 'completed')
            ->assertJsonPath('progress_percent', 100)
            ->assertJsonPath('report_available', true);

        $this->assertDatabaseHas('resume_scores', [
            'resume_id' => $resume->id,
        ]);
    }

    public function test_live_dashboard_and_notification_json_endpoints_work_for_user_and_admin(): void
    {
        $this->seed();

        $candidate = User::where('role', 'candidate')->firstOrFail();
        $admin = User::where('role', 'admin')->firstOrFail();

        AppNotification::create([
            'user_id' => $candidate->id,
            'title' => 'Test Notification',
            'message' => 'Notification badge refresh test.',
            'type' => 'info',
            'is_read' => false,
        ]);

        $this->actingAs($candidate);

        $this->getJson('/user/dashboard/stats')
            ->assertOk()
            ->assertJsonStructure([
                'total_resumes',
                'completed_analyses',
                'pending_analyses',
                'average_score',
                'recent_resumes',
                'last_updated',
            ]);

        $this->getJson('/notifications/unread-count')
            ->assertOk()
            ->assertJsonPath('unread_count', 1);

        $this->postJson('/notifications/mark-all-read')
            ->assertOk()
            ->assertJsonPath('status', 'ok');

        $this->getJson('/notifications/unread-count')
            ->assertOk()
            ->assertJsonPath('unread_count', 0);

        $this->actingAs($admin);

        $this->getJson('/admin/dashboard/stats')
            ->assertOk()
            ->assertJsonStructure([
                'total_users',
                'total_candidates',
                'total_admins',
                'total_resumes',
                'completed_analyses',
                'pending_analyses',
                'active_job_roles',
                'recent_resumes',
                'last_updated',
            ]);
    }

    public function test_skill_matcher_handles_common_ocr_variations_for_resume_skills(): void
    {
        $this->seed();

        $jobRole = JobRole::where('title', 'Data Scientist')->firstOrFail();
        $result = app(SkillMatcherService::class)->match(
            'Python MYSQL / SOL Data-Analysis',
            $jobRole
        );

        $this->assertContains('Python', $result['detected_skills']);
        $this->assertContains('SQL', $result['detected_skills']);
        $this->assertContains('Data Analysis', $result['detected_skills']);
        $this->assertContains('Python', $result['matched_skills']);
        $this->assertContains('SQL', $result['matched_skills']);
        $this->assertContains('Data Analysis', $result['matched_skills']);
    }

    public function test_generic_resume_skills_from_skill_section_increase_score(): void
    {
        $this->seed();

        $jobRole = JobRole::where('title', 'Data Scientist')->firstOrFail();
        $resumeText = <<<TEXT
        Herman Walton
        Student
        (917)324-1818 hw_alton77_x@yahoo.com

        Skills
        Advanced Communication
        Office Technology Skills
        Social Media Platforms
        French
        Dutch

        Profile
        Hardworking student seeking employment.

        Education
        Bachelor of Communications, New York University
        TEXT;

        $matchResult = app(SkillMatcherService::class)->match($resumeText, $jobRole);
        $scoreResult = app(ResumeScorerService::class)->calculate([
            'full_name' => 'Herman Walton',
            'email' => 'hw_alton77_x@yahoo.com',
            'phone' => '(917)324-1818',
            'address' => '',
            'education' => 'Bachelor of Communications, New York University',
            'experience' => '',
            'projects' => '',
            'certifications' => '',
            'skills_section' => "Advanced Communication\nOffice Technology Skills\nSocial Media Platforms\nFrench\nDutch",
            'summary' => 'Hardworking student seeking employment.',
            'raw_text' => $resumeText,
        ], $matchResult);

        $this->assertContains('Advanced Communication', $matchResult['detected_skills']);
        $this->assertContains('Office Technology Skills', $matchResult['detected_skills']);
        $this->assertContains('Social Media Platforms', $matchResult['detected_skills']);
        $this->assertContains('French', $matchResult['detected_skills']);
        $this->assertContains('Dutch', $matchResult['detected_skills']);
        $this->assertGreaterThan(0, $scoreResult['skills_score']);
        $this->assertGreaterThan(7, $scoreResult['total_score']);
    }
}
