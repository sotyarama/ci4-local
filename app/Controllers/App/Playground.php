<?php

namespace App\Controllers\App;

use App\Controllers\BaseController;

class Playground extends BaseController
{
    public function index()
    {
        return view('app/playground', [
            'title' => 'App Playground (Shell Refactor)',
        ]);
    }
}
