<?php

namespace App\Controllers;

class Guides extends BaseController
{
    public function branding()
    {
        // Branding is intentionally a web-only module (no PDF export).
        // Keep this controller simple and free of PDF rendering logic.
        helper('url');
        $data = [
            'title' => 'Branding',
            'subtitle' => 'Panduan ringkas identitas visual dan komunikasi.',
        ];

        return view('guides/branding', $data);
    }

    public function howToUse()
    {
        // How-To is intentionally a web-only module (no PDF export).
        helper('url');
        $data = [
            'title' => 'How to Use',
            'subtitle' => 'Panduan singkat penggunaan aplikasi sehari-hari.',
        ];

        return view('guides/how_to_use', $data);
    }
}

