<?php

namespace App\Console\Commands;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Console\Command;

class WarmPdfFonts extends Command
{
    protected $signature = 'pdf:warm-fonts';
    protected $description = 'Pre-generate the dompdf font-metrics cache so the first PDF render after a deploy never 500s';

    public function handle(): int
    {
        // dompdf keys its .ufm cache off the (release-specific) font path, so the
        // cache must be rebuilt after every deploy. Render the brand font in all
        // four styles once to force generation into storage/fonts.
        $fonts = view('pdf.partials.fonts')->render();
        $html = $fonts
            .'<p style="font-family:Nunito">a</p>'
            .'<p style="font-family:Nunito;font-weight:bold">b</p>'
            .'<p style="font-family:Nunito;font-style:italic">c</p>'
            .'<p style="font-family:Nunito;font-weight:bold;font-style:italic">d</p>';

        try {
            Pdf::loadHTML($html)->output();
        } catch (\Throwable $e) {
            $this->error('PDF font warm-up failed: '.$e->getMessage());

            return self::FAILURE;
        }

        $this->info('✅ PDF font cache warmed.');

        return self::SUCCESS;
    }
}
