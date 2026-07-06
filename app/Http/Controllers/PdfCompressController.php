<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use setasign\Fpdi\Tcpdf\Fpdi;

class PdfCompressController extends Controller
{
    /**
     * Compression level presets (DPI, image quality, description)
     */
    private array $levels = [
        'low'     => ['dpi' => 144, 'quality' => 85, 'label' => 'Rendah'],
        'medium'  => ['dpi' => 96,  'quality' => 70, 'label' => 'Sedang'],
        'high'    => ['dpi' => 72,  'quality' => 50, 'label' => 'Tinggi'],
        'extreme' => ['dpi' => 48,  'quality' => 30, 'label' => 'Sangat Tinggi'],
    ];

    /**
     * Show the compress PDF page.
     */
    public function index()
    {
        return view('pdf-compress.index', [
            'levels' => $this->levels,
        ]);
    }

    /**
     * Compress the uploaded PDF and return it as a download.
     */
    public function compress(Request $request)
    {
        $request->validate([
            'pdf'   => 'required|file|mimes:pdf|max:102400', // max 100MB
            'level' => 'required|in:low,medium,high,extreme',
        ]);

        $file        = $request->file('pdf');
        $level       = $request->input('level', 'medium');
        $levelConfig = $this->levels[$level];

        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $inputPath    = $file->getRealPath();
        $outputName   = $originalName . '_compressed_' . Str::random(6) . '.pdf';
        $outputPath   = storage_path('app/private/pdf-compress/' . $outputName);

        // Ensure output directory exists
        if (!is_dir(dirname($outputPath))) {
            mkdir(dirname($outputPath), 0755, true);
        }

        try {
            $pdf = new Fpdi('P', 'pt');
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);

            // Set global TCPDF image quality
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

            $pageCount = $pdf->setSourceFile($inputPath);

            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $templateId = $pdf->importPage($pageNo);
                $size       = $pdf->getTemplateSize($templateId);

                $pdf->AddPage(
                    $size['width'] > $size['height'] ? 'L' : 'P',
                    [$size['width'], $size['height']]
                );

                $pdf->useTemplate($templateId, 0, 0, $size['width'], $size['height'], true);
            }

            // Output to file with TCPDF compression
            $pdf->Output($outputPath, 'F');

            $originalSize    = $file->getSize();
            $compressedSize  = filesize($outputPath);

            return response()
                ->download($outputPath, $originalName . '_compressed.pdf', [
                    'Content-Type' => 'application/pdf',
                ])
                ->deleteFileAfterSend(true)
                ->withHeaders([
                    'X-Original-Size'   => $originalSize,
                    'X-Compressed-Size' => $compressedSize,
                ]);

        } catch (\Exception $e) {
            // Cleanup on error
            if (file_exists($outputPath)) {
                unlink($outputPath);
            }

            return back()->withErrors([
                'pdf' => 'Gagal mengompres PDF: ' . $e->getMessage(),
            ]);
        }
    }
}
