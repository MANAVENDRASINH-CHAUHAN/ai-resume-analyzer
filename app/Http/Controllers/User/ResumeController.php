<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreResumeRequest;
use App\Models\ActivityLog;
use App\Models\AppNotification;
use App\Models\JobRole;
use App\Models\Resume;
use App\Models\User;
use App\Services\ResumeAnalysisService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class ResumeController extends Controller
{
    public function index(): View
    {
        $resumes = Resume::query()
            ->with(['jobRole', 'resumeScore', 'analysisReport'])
            ->where('user_id', auth()->id())
            ->orderByDesc('uploaded_at')
            ->orderByDesc('id')
            ->paginate(10);

        return view('user.resumes.index', compact('resumes'));
    }

    public function create(): View
    {
        $jobRoles = JobRole::active()
            ->orderBy('title')
            ->get();

        return view('user.resumes.create', compact('jobRoles'));
    }

    public function store(StoreResumeRequest $request): RedirectResponse
    {
        $uploadedFile = $request->file('resume_file');
        $extension = strtolower($uploadedFile->getClientOriginalExtension());
        $uniqueFileName = 'resume_' . Str::uuid() . '.' . $extension;
        $storedPath = $uploadedFile->storeAs('resumes', $uniqueFileName, 'public');

        $resume = Resume::create([
            'user_id' => $request->user()->id,
            'job_role_id' => $request->integer('job_role_id'),
            'file_name' => $uploadedFile->getClientOriginalName(),
            'file_path' => $storedPath,
            'file_type' => $extension,
            'file_size' => (int) $uploadedFile->getSize(),
            'upload_status' => 'uploaded',
            'analysis_status' => 'pending',
            'progress_percent' => 0,
            'uploaded_at' => now(),
        ]);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'resume_id' => $resume->id,
            'activity_type' => 'resume_upload',
            'activity_message' => 'Resume uploaded successfully for future analysis.',
            'ip_address' => $request->ip(),
        ]);

        $this->notifyUser(
            $request->user()->id,
            'Resume Uploaded',
            'Your resume "' . $resume->file_name . '" was uploaded successfully.',
            'info'
        );

        $this->notifyAdmins(
            'New Resume Uploaded',
            $request->user()->name . ' uploaded a new resume for ' . ($resume->jobRole?->title ?? 'general review') . '.',
            'info'
        );

        return redirect()
            ->route('user.resumes.show', $resume)
            ->with('success', 'Resume uploaded successfully.');
    }

    public function show(Resume $resume): View
    {
        $resume = $this->findUserResume($resume->id)->load([
            'extractedResumeData',
            'resumeScore',
            'analysisReport',
            'resumeSkillMaps.skill',
        ]);

        return view('user.resumes.show', compact('resume'));
    }

    public function analyze(
        Request $request,
        Resume $resume,
        ResumeAnalysisService $resumeAnalysisService
    ): RedirectResponse|JsonResponse {
        $validated = $request->validate([
            'manual_text' => ['nullable', 'string', 'max:25000'],
        ]);

        $resume = $this->findUserResume($resume->id);

        if ($this->isAjaxRequest($request)) {
            $resumeAnalysisService->startLiveAnalysis($resume, $validated['manual_text'] ?? null, $request->ip());

            return response()->json([
                'message' => 'Analysis started successfully. Live status will refresh automatically.',
                'resume' => $this->formatResumeStatusPayload($resume->fresh(['resumeScore', 'analysisReport'])),
            ]);
        }

        try {
            $resumeAnalysisService->analyze($resume, $validated['manual_text'] ?? null);

            return redirect()
                ->route('user.resumes.show', $resume)
                ->with('success', 'Resume analyzed successfully.');
        } catch (Throwable $exception) {
            return redirect()
                ->route('user.resumes.show', $resume)
                ->withInput()
                ->with('error', $exception->getMessage());
        }
    }

    public function status(Request $request, Resume $resume, ResumeAnalysisService $resumeAnalysisService): JsonResponse
    {
        $resume = $this->findUserResume($resume->id)->loadMissing(['resumeScore', 'analysisReport']);

        if ($resume->analysis_status === 'in_progress') {
            $resumeAnalysisService->advanceLiveAnalysis($resume, $request->ip());
            $resume->refresh()->load(['resumeScore', 'analysisReport']);
        }

        return response()->json($this->formatResumeStatusPayload($resume));
    }

    public function statusList(Request $request, ResumeAnalysisService $resumeAnalysisService): JsonResponse
    {
        $ids = collect(explode(',', (string) $request->query('ids')))
            ->map(fn (string $value) => (int) trim($value))
            ->filter(fn (int $value) => $value > 0)
            ->take(20)
            ->values();

        $resumes = Resume::query()
            ->with(['resumeScore', 'analysisReport'])
            ->where('user_id', auth()->id())
            ->when($ids->isNotEmpty(), fn ($query) => $query->whereIn('id', $ids->all()))
            ->get();

        $payload = $resumes->map(function (Resume $resume) use ($request, $resumeAnalysisService): array {
            if ($resume->analysis_status === 'in_progress') {
                $resumeAnalysisService->advanceLiveAnalysis($resume, $request->ip());
                $resume->refresh()->load(['resumeScore', 'analysisReport']);
            }

            return $this->formatResumeStatusPayload($resume);
        })->values();

        return response()->json([
            'resumes' => $payload,
        ]);
    }

    public function download(Resume $resume): StreamedResponse
    {
        $resume = $this->findUserResume($resume->id);

        abort_unless(Storage::disk('public')->exists($resume->file_path), 404);

        return Storage::disk('public')->download($resume->file_path, $resume->file_name);
    }

    public function destroy(Resume $resume): RedirectResponse
    {
        $resume = $this->findUserResume($resume->id);

        if ($resume->file_path && Storage::disk('public')->exists($resume->file_path)) {
            Storage::disk('public')->delete($resume->file_path);
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'resume_id' => $resume->id,
            'activity_type' => 'resume_delete',
            'activity_message' => 'Resume deleted by candidate user.',
            'ip_address' => request()->ip(),
        ]);

        $resume->delete();

        return redirect()
            ->route('user.resumes.index')
            ->with('success', 'Resume deleted successfully.');
    }

    protected function findUserResume(int $resumeId): Resume
    {
        return Resume::query()
            ->with(['jobRole', 'resumeScore', 'analysisReport'])
            ->where('user_id', auth()->id())
            ->findOrFail($resumeId);
    }

    protected function isAjaxRequest(Request $request): bool
    {
        return $request->ajax() || $request->expectsJson();
    }

    protected function formatResumeStatusPayload(Resume $resume): array
    {
        $resume->loadMissing(['resumeScore', 'analysisReport']);

        return [
            'id' => $resume->id,
            'upload_status' => $resume->upload_status,
            'analysis_status' => $resume->analysis_status,
            'upload_status_label' => str($resume->upload_status)->replace('_', ' ')->title()->toString(),
            'analysis_status_label' => str($resume->analysis_status)->replace('_', ' ')->title()->toString(),
            'progress_percent' => (int) $resume->progress_percent,
            'total_score' => $resume->resumeScore?->total_score,
            'report_available' => $resume->analysis_status === 'completed' && $resume->analysisReport !== null,
            'report_url' => $resume->analysis_status === 'completed' ? route('user.reports.show', $resume) : null,
            'print_url' => $resume->analysis_status === 'completed' ? route('user.reports.print', $resume) : null,
            'resume_url' => route('user.resumes.show', $resume),
            'download_url' => $resume->download_url,
            'upload_status_badge_class' => $resume->upload_status_badge_class,
            'analysis_status_badge_class' => $resume->analysis_status_badge_class,
            'status_label' => match ($resume->analysis_status) {
                'completed' => 'Completed',
                'error' => 'Error',
                'in_progress' => 'Analyzing',
                default => 'Pending',
            },
            'status_color' => match ($resume->analysis_status) {
                'completed' => 'success',
                'error' => 'danger',
                'in_progress' => 'warning',
                default => 'secondary',
            },
            'updated_at' => optional($resume->updated_at)->format('d M Y, h:i:s A'),
        ];
    }

    protected function notifyUser(int $userId, string $title, string $message, string $type = 'info'): void
    {
        AppNotification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'is_read' => false,
        ]);
    }

    protected function notifyAdmins(string $title, string $message, string $type = 'info'): void
    {
        User::query()
            ->admins()
            ->active()
            ->pluck('id')
            ->each(function (int $adminId) use ($title, $message, $type): void {
                AppNotification::create([
                    'user_id' => $adminId,
                    'title' => $title,
                    'message' => $message,
                    'type' => $type,
                    'is_read' => false,
                ]);
            });
    }
}
