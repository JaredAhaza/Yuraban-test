<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class DriverController extends Controller
{
    //
    public function index(Request $request)
    {
        $drivers = User::where('role', 'driver')->orderBy('created_at', 'desc')->get();
        return view('drivers.index', compact('drivers'));
    }
}
