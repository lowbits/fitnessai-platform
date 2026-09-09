<?php

namespace App\Support\PlanEvaluator;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Parser;

/**
 * Pulls plain text out of an uploaded document (pdf or txt) so it can go through
 * the same text-extraction path as a pasted plan. Images never reach here — they
 * go straight to the vision extractor.
 */
class PlanTextExtractor
{
    public function fromUpload(UploadedFile $file): string
    {
        if ($this->isPdf($file)) {
            return $this->fromPdf($file);
        }

        return (string) file_get_contents($file->getRealPath());
    }

    private function isPdf(UploadedFile $file): bool
    {
        return $file->getClientMimeType() === 'application/pdf'
            || strtolower((string) $file->getClientOriginalExtension()) === 'pdf';
    }

    private function fromPdf(UploadedFile $file): string
    {
        try {
            return (new Parser)->parseFile($file->getRealPath())->getText();
        } catch (\Throwable $e) {
            Log::warning('[PlanRoast][Pdf] Extraction failed', ['message' => $e->getMessage()]);

            return '';
        }
    }
}
