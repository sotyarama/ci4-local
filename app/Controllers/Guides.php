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

        if ($this->request->getGet('export') === 'print') {
            $data['assetVer'] = time();
            $data['extraStylesheets'] = [
                base_url('css/branding.css') . '?v=' . $data['assetVer'],
            ];
            $data['logoSrc'] = base_url('images/temurasa_primary_fit.png');
            $data['backUrl'] = site_url('branding');

            return view('guides/branding_print', $data);
        }

        if ($this->request->getGet('export') === 'pdf') {
            $data['extraCss'] = $this->loadBrandingCss();
            $data['logoSrc'] = $this->resolveBrandingLogoPath();
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

        if ($this->request->getGet('export') === 'print') {
            $data['assetVer'] = time();
            $data['forceLongPage'] = true;
            $data['pageWidth'] = '210mm';
            $data['pageHeight'] = '2000mm';
            $data['pageMargin'] = '12mm';
            $data['backUrl'] = site_url('how-to-use');
            return view('guides/how_to_use_print', $data);
        }

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
        $dompdf->loadHtml(view($view, $data));
        $dompdf->render();

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($dompdf->output());
    }

    private function loadBrandingCss(): string
    {
        $cssPath = FCPATH . 'css/branding.css';
        if (is_file($cssPath)) {
            $contents = file_get_contents($cssPath);
            return is_string($contents) ? $contents : '';
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
}
