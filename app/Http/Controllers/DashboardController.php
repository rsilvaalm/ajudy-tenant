<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $customization = DB::table('customization')->first();

        return view('dashboard', compact('customization'));
    }
}
