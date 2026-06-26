<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CertificateController extends Controller
{
    public function download(Enrollment $enrollment)
    {
        abort_unless($enrollment->user_id === auth()->id(), 403);
        abort_unless($enrollment->progress_percent >= 100 || $enrollment->status === 'completed', 403, 'Cours non terminé.');

        $enrollment->load('user', 'course');

        $verifyUrl = route('certificate.verify', [
            'enrollment' => $enrollment->id,
            'token'      => $this->token($enrollment),
        ]);

        // SVG format — works without imagick (GD is sufficient)
        $qrCode = QrCode::format('svg')->size(120)->errorCorrection('H')->generate($verifyUrl);

        $completedAt = $enrollment->completed_at ?? $enrollment->updated_at;

        $pdf = Pdf::loadView('certificate.pdf', compact('enrollment', 'qrCode', 'completedAt', 'verifyUrl'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download('certificat-' . Str::slug($enrollment->course->title) . '.pdf');
    }

    public function verify(Enrollment $enrollment, Request $request)
    {
        abort_unless($request->get('token') === $this->token($enrollment), 403, 'Certificat invalide.');

        $enrollment->load('user', 'course');

        return view('certificate.verify', compact('enrollment'));
    }

    private function token(Enrollment $enrollment): string
    {
        return hash('sha256', $enrollment->id . '-' . $enrollment->user_id . '-' . config('app.key'));
    }
}
