<?php

namespace App\Http\Controllers;

use App\Models\PageContent;

class HomeController extends Controller
{
    public function index()
    {
        $page = PageContent::where('type', 'homepage')->first();
        return view('index', ['page' => $page]);
    }
}
