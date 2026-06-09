<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\LessonProgress;
use App\Models\Enrollment;
use App\Models\CertificateTemplate;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class CertificateController extends Controller
{
    /**
     * Display a list of the student's certificates.
     */
    public function index()
    {
        $user = auth()->user();

        // Get enrollments with course and lesson details
        $enrollments = Enrollment::with(['course.lessons', 'course.user'])
            ->where('user_id', $user->id)
            ->get();

        $certificates = [];

        foreach ($enrollments as $enrollment) {
            $course = $enrollment->course;
            $totalLessons = $course->lessons->count();

            if ($totalLessons > 0) {
                $completedCount = LessonProgress::where('user_id', $user->id)
                    ->whereIn('lesson_id', $course->lessons->pluck('id'))
                    ->count();

                $progress = round(($completedCount / $totalLessons) * 100);

                if ($progress >= 100) {
                    // Auto-generate certificate record if not exists
                    $certificate = Certificate::firstOrCreate(
                        [
                            'user_id'   => $user->id,
                            'course_id' => $course->id,
                        ],
                        [
                            'code'      => 'CERT-' . strtoupper(Str::random(10)),
                            'issued_at' => now(),
                        ]
                    );

                    $certificate->load(['course.user', 'user']);
                    $certificates[] = $certificate;
                }
            }
        }

        return view('pages.certificates', compact('certificates'));
    }

    /**
     * Show a certificate in web view.
     */
    public function show($code)
    {
        $certificate = Certificate::with(['course.user', 'user'])
            ->where('code', $code)
            ->firstOrFail();

        // Security check: Only the student, the course author, or admin can view this certificate
        $user = auth()->user();
        if ($user->id !== $certificate->user_id && $user->id !== $certificate->course->user_id && !$user->isAdmin()) {
            abort(403, __('You do not have permission to view this certificate.'));
        }

        $design = $this->getActiveTemplate();

        return view('certificates.show', compact('certificate', 'design'));
    }

    /**
     * Download the certificate as PDF using dompdf.
     */
    public function download($code)
    {
        $certificate = Certificate::with(['course.user', 'user'])
            ->where('code', $code)
            ->firstOrFail();

        // Security check
        $user = auth()->user();
        if ($user->id !== $certificate->user_id && $user->id !== $certificate->course->user_id && !$user->isAdmin()) {
            abort(403, __('You do not have permission to download this certificate.'));
        }

        $design = $this->getActiveTemplate();

        // Dompdf options
        $pdf = Pdf::loadView('certificates.pdf', compact('certificate', 'design'))
            ->setPaper('a4', 'landscape')
            ->setWarnings(false);

        return $pdf->download('certificate-' . $certificate->code . '.pdf');
    }

    /**
     * Retrieve the active certificate template design parameters.
     */
    private function getActiveTemplate(): array
    {
        $template = CertificateTemplate::where('is_default', true)
            ->where('is_active', true)
            ->first();

        if (!$template) {
            $template = CertificateTemplate::where('is_active', true)->latest()->first();
        }

        $defaults = [
            'background_color'  => '#fbfbfd',
            'border_outer_color'=> '#1e293b',
            'border_inner_color'=> '#d97706',
            'text_color'        => '#1e293b',
            'recipient_color'   => '#d97706',
            'accent_color'      => '#d97706',
            'seal_color'        => '#d97706',
            'logo_color'        => '#FF6B35',
            'template_name'     => 'EduPlatform',
        ];

        if ($template && is_array($template->layout)) {
            // Merge template settings over defaults
            return array_merge($defaults, $template->layout, ['template_name' => $template->name]);
        }

        return $defaults;
    }
}
