<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\View\View;

class BrandTreeController extends Controller
{
    public function root(Brand $brand): View
    {
        return view('brands.trees.root', compact('brand'));
    }

    public function trunk(Brand $brand): View
    {
        return view('brands.trees.trunk', compact('brand'));
    }

    public function canopy(Brand $brand): View
    {
        return view('brands.trees.canopy', compact('brand'));
    }
}
