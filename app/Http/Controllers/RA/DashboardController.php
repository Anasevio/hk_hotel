<?php

namespace App\Http\Controllers\RA;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('RA.dashboard_ra');
    }
}
