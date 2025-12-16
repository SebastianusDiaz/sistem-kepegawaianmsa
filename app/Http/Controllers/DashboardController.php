<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //
    public function index()
    {
        $role = auth()->user()->role;
        return view('dashboard.index', compact('role'));
    }
}
