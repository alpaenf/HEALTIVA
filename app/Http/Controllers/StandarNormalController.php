<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class StandarNormalController extends Controller
{
    public function index()
    {
        return Inertia::render('StandarNormal');
    }
}
