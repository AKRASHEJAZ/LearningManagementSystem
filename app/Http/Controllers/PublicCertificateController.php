<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Evaluation;
use App\Models\EvaluationGrade;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\SettingsService;
use Illuminate\Support\Facades\Storage;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class PublicCertificateController extends Controller
{
    public function show(Certificate $certificate)
    {
        $certificate->load(['user:id,name', 'course:id,title,slug']);

        $evaluations = Evaluation::query()
            ->where('course_id', $certificate->course_id)
            ->orderBy('position')
            ->orderBy('id')
            ->get(['id', 'title', 'description', 'max_score']);

        $grades = EvaluationGrade::query()
            ->where('course_id', $certificate->course_id)
            ->where('student_user_id', $certificate->user_id)
            ->get(['evaluation_id', 'status', 'score', 'feedback', 'graded_at'])
            ->keyBy('evaluation_id');

        $issuedAt = $certificate->issued_at ?? $certificate->created_at;

        $sumScore = 0;
        $sumMax = 0;
        $hasAnyScore = false;
        foreach ($evaluations as $evaluation) {
            $grade = $grades->get($evaluation->id);
            if ($grade && $grade->score !== null) {
                $hasAnyScore = true;
                $sumScore += (int) $grade->score;
            }
            if ($evaluation->max_score !== null) {
                $sumMax += (int) $evaluation->max_score;
            }
        }

        $summary = [
            'has_any_score' => $hasAnyScore,
            'sum_score' => $hasAnyScore ? $sumScore : null,
            'sum_max' => $sumMax > 0 ? $sumMax : null,
        ];

        return view('certificates.verify.show', [
            'certificate' => $certificate,
            'evaluations' => $evaluations,
            'grades' => $grades,
            'issuedAt' => $issuedAt,
            'summary' => $summary,
            'verifyUrl' => route('certificates.verify.show', $certificate, absolute: true),
        ]);
    }

    public function pdf(Certificate $certificate)
    {
        $certificate->load(['user:id,name', 'course:id,title,slug']);

        $verifyUrl = route('certificates.verify.show', $certificate, absolute: true);

        $filename = 'certificate-'.$certificate->uuid.'.pdf';

        $regenerate = request()->boolean('regenerate');

        if (! $regenerate && $certificate->pdf_path && Storage::disk('public')->exists($certificate->pdf_path)) {
            return Storage::disk('public')->download($certificate->pdf_path, $filename);
        }

        $qrDataUri = (new QRCode(new QROptions([
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'scale' => 6,
            'quietzoneSize' => 2,
        ])))->render($verifyUrl);

        $evaluations = Evaluation::query()
            ->where('course_id', $certificate->course_id)
            ->orderBy('position')
            ->orderBy('id')
            ->get(['id', 'title', 'description', 'max_score']);

        $grades = EvaluationGrade::query()
            ->where('course_id', $certificate->course_id)
            ->where('student_user_id', $certificate->user_id)
            ->get(['evaluation_id', 'status', 'score', 'feedback', 'graded_at'])
            ->keyBy('evaluation_id');

        $issuedAt = $certificate->issued_at ?? $certificate->created_at;

        $sumScore = 0;
        $sumMax = 0;
        $hasAnyScore = false;
        foreach ($evaluations as $evaluation) {
            $grade = $grades->get($evaluation->id);
            if ($grade && $grade->score !== null) {
                $hasAnyScore = true;
                $sumScore += (int) $grade->score;
            }
            if ($evaluation->max_score !== null) {
                $sumMax += (int) $evaluation->max_score;
            }
        }

        $summary = [
            'has_any_score' => $hasAnyScore,
            'sum_score' => $hasAnyScore ? $sumScore : null,
            'sum_max' => $sumMax > 0 ? $sumMax : null,
        ];

        /** @var SettingsService $settings */
        $settings = app(SettingsService::class);
        $appSettings = $settings->all();

        $brandName = $appSettings['institute.name'] ?? config('app.name', 'Learning Platform');
        $primaryColor = $appSettings['brand.primary_color'] ?? '#0d6efd';
        $logoPath = $appSettings['brand.logo_path'] ?? null;

        $logoDataUri = null;
        if ($logoPath) {
            $absolute = public_path('storage/'.$logoPath);
            if (is_file($absolute)) {
                $mime = mime_content_type($absolute) ?: 'image/png';
                $logoDataUri = 'data:'.$mime.';base64,'.base64_encode(file_get_contents($absolute));
            }
        }

        $pdf = Pdf::loadView('certificates.certificate.pdf', [
            'certificate' => $certificate,
            'verifyUrl' => $verifyUrl,
            'qrDataUri' => $qrDataUri,
            'brandName' => $brandName,
            'primaryColor' => $primaryColor,
            'logoDataUri' => $logoDataUri,
            'evaluations' => $evaluations,
            'grades' => $grades,
            'issuedAt' => $issuedAt,
            'summary' => $summary,
        ])
            ->setPaper('a4', 'landscape')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('defaultFont', 'Helvetica')
            ->setOption('isFontSubsettingEnabled', false);

        // Store for shared hosting friendliness (optional cache). If storage fails, still stream the PDF.
        if ($regenerate || ! $certificate->pdf_path) {
            try {
                $path = 'certificates/'.$filename;
                Storage::disk('public')->put($path, $pdf->output());
                $certificate->forceFill(['pdf_path' => $path])->save();
            } catch (\Throwable) {
                // no-op
            }
        }

        return $pdf->download($filename);
    }
}
