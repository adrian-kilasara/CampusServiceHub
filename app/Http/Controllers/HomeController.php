<?php

namespace App\Http\Controllers;

use App\Models\ServiceCategory;
use App\Models\SystemSetting;

class HomeController extends Controller
{
    public function index()
    {
        $categories = ServiceCategory::where('is_active', true)
            ->withCount('services')
            ->orderBy('sort_order')
            ->get();

        $siteName = SystemSetting::get('site_name', 'CampusHub');
        $tagline  = SystemSetting::get('site_tagline', 'Smart Campus Service Platform');

        return view('home', compact('categories', 'siteName', 'tagline'));
    }
}
