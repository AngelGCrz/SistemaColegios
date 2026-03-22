<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Support\Facades\Cache;

class LandingController extends Controller
{
    public function index()
    {
        $planes = Cache::remember('landing_planes', 3600, function () {
            return Plan::where('activo', true)->orderBy('orden')->get();
        });

        return view('landing', compact('planes'));
    }
}
