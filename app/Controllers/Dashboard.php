<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index(): string
    {
        // Nanti ini bisa diisi data penjualan, ringkasan, dsb.
        $data = [
            'title' => 'Cafe POS Dashboard',
            'subtitle' => 'Selamat datang di sistem POS lokal',
        ];

        return view('dashboard', $data);
    }
}
