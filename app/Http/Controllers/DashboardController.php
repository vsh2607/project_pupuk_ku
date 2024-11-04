<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(){
        $totalRegisteredFarmer = \App\Models\MasterFarmer::count();

        return view('dashboard.index');
    }
}

