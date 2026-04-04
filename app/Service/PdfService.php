<?php

namespace App\Service;

use App\Models\QuizSession;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class PdfService
{
    /**
     * Generate a quiz result PDF and return it as a streamed download.
     * The display name is never written to the database.
     */
    public function generate(QuizSession $session, ?string $rawDisplayName): Response
    {
        // Sanitize display name — strip tags, limit length
        $displayName = $rawDisplayName
            ? substr(strip_tags($rawDisplayName), 0, 100)
            : 'Anonymous';

        $pdf = Pdf::loadView('pdf.quiz-result', [
            'session'     => $session,
            'displayName' => $displayName,
            'generatedAt' => now()->format('F d, Y h:i A'),
            'disclaimer'  => 'This result is NOT a medical diagnosis. This tool is for educational and portfolio purposes only. Please consult a qualified mental health professional for clinical evaluation.',
        ]);

        // Stream as attachment — never saved to disk
        return $pdf->download('mindcheck-result.pdf');
    }
}
