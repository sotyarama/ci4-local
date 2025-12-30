<?php

namespace App\Controllers;

class Guides extends BaseController
{
    public function branding()
    {
        return view('guides/branding', [
            'title' => 'Branding',
            'subtitle' => 'Panduan ringkas identitas visual dan komunikasi.',
        ]);
    }

    public function howToUse()
    {
        return view('guides/how_to_use', [
            'title' => 'How to Use',
            'subtitle' => 'Panduan singkat penggunaan aplikasi sehari-hari.',
        ]);
    }
}
