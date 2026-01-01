<?php

namespace App\Controllers;

use Dompdf\Dompdf;
use Dompdf\Options;

class Guides extends BaseController
{
    public function branding()
    {
        helper('url');
        $data = [
            'title' => 'Branding',
            'subtitle' => 'Panduan ringkas identitas visual dan komunikasi.',
        ];

        // Print export removed: UI no longer exposes print; PDF export remains.

        if ($this->request->getGet('export') === 'pdf') {
            $data['extraCss'] = $this->loadBrandingCss();
            // Inline logo as data URI to avoid file/http loading delays during PDF render
            $logoFile = FCPATH . 'images/temurasa_primary_fit.png';
            $data['logoSrc'] = is_file($logoFile) ? $this->fileToDataUri($logoFile) : $this->resolveBrandingLogoPath();
            $filename = 'branding_guide_' . date('Ymd') . '.pdf';

            return $this->renderPdf('guides/pdf/branding', $data, $filename);
        }

        return view('guides/branding', $data);
    }

    public function howToUse()
    {
        helper('url');
        $data = [
            'title' => 'How to Use',
            'subtitle' => 'Panduan singkat penggunaan aplikasi sehari-hari.',
        ];

        // Print export removed: UI no longer exposes print; PDF export remains.

        if ($this->request->getGet('export') === 'pdf') {
            $filename = 'how_to_use_' . date('Ymd') . '.pdf';
            return $this->renderPdf('guides/pdf/how_to_use', $data, $filename);
        }

        return view('guides/how_to_use', $data);
    }

    private function renderPdf(string $view, array $data, string $filename, string $paper = 'A4', string $orientation = 'portrait')
    {
        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->setIsRemoteEnabled(true);

        $dompdf = new Dompdf($options);
        $dompdf->setPaper($paper, $orientation);
        // Debugging: capture HTML and timings to help diagnose slow renders
        try {
            $start = microtime(true);
            $html = view($view, $data);

            // Save the HTML used for rendering for inspection
            $htmlFile = WRITEPATH . 'logs/debug_pdf_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.html';
            @file_put_contents($htmlFile, $html);
            log_message('info', 'renderPdf: saved HTML to ' . $htmlFile . ' (len=' . strlen($html) . ')');

            $dompdf->loadHtml($html);

            $dompdf->render();

            $pdfOutput = $dompdf->output();
            $duration = microtime(true) - $start;

            // Save the generated PDF for inspection
            $pdfFile = WRITEPATH . 'logs/debug_pdf_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.pdf';
            @file_put_contents($pdfFile, $pdfOutput);
            log_message('info', sprintf('renderPdf: rendered %s in %.2f sec; pdf_len=%d saved=%s', $view, $duration, strlen($pdfOutput), $pdfFile));

            return $this->response
                ->setHeader('Content-Type', 'application/pdf')
                ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->setBody($pdfOutput);
        } catch (\Throwable $e) {
            // Log exception to help debugging
            log_message('error', 'renderPdf: exception - ' . $e->getMessage());
            throw $e;
        }
    }

    private function loadBrandingCss(): string
    {
        $cssPath = FCPATH . 'css/branding.css';
        if (is_file($cssPath)) {
            $contents = file_get_contents($cssPath);
            if (! is_string($contents)) {
                return '';
            }

            // Remove @import rules and @font-face blocks to avoid remote fetching
            $contents = preg_replace('/@import[^;]+;/i', '', $contents);
            $contents = preg_replace('/@font-face\s*\{[^}]*\}/is', '', $contents);

            // Rewrite local url(...) references to file:/// absolute paths so Dompdf
            // loads them from disk instead of via HTTP (avoids long remote loads).
            $contents = preg_replace_callback('/url\(([^)]+)\)/i', function ($m) {
                $url = trim($m[1], "'\" \t\n\r");

                // Leave data URIs and absolute remote URLs alone
                if (preg_match('#^(data:|https?:|//)#i', $url)) {
                    return "url($url)";
                }

                // Normalize path and attempt to resolve against FCPATH
                $path = $url;
                if (strpos($path, '/') === 0) {
                    $path = ltrim($path, '/');
                }

                $full = realpath(FCPATH . $path);
                if ($full !== false) {
                    $normalized = str_replace('\\\\', '/', $full);
                    return 'url("file:///' . $normalized . '")';
                }

                return "url($url)";
            }, $contents);

            return $contents;
        }

        return '';
    }

    private function resolveBrandingLogoPath(): string
    {
        helper('url');
        $logoPath = FCPATH . 'images/temurasa_primary_fit.png';
        $realPath = realpath($logoPath);
        if ($realPath !== false) {
            $normalized = str_replace('\\', '/', $realPath);
            return 'file:///' . $normalized;
        }

        return base_url('images/temurasa_primary_fit.png');
    }

    private function fileToDataUri(string $filePath): string
    {
        $mime = mime_content_type($filePath) ?: 'application/octet-stream';
        $data = file_get_contents($filePath);
        if ($data === false) {
            return '';
        }

        return 'data:' . $mime . ';base64,' . base64_encode($data);
    }
}
