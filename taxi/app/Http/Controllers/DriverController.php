<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\County;
use Illuminate\Support\Facades\DB;

class DriverController extends Controller
{
    public function index(Request $request)
    {
        // Get all counties for the filter dropdown
        $counties = County::all();
        
        // Start with a more direct query and add debug logging
        $query = User::where('role', 'driver');
        
        // Debug: Log the initial count
        $initialCount = $query->count();
        
        // Apply county filter if selected
        if ($request->filled('county_id')) {
            $query->where('county_id', $request->county_id);
            
            // Apply subcounty filter if selected
            if ($request->filled('subcounty')) {
                $query->where('subcounty', $request->subcounty);
            }
        }
        
        // Get results ordered by creation date
        $drivers = $query->orderBy('created_at', 'desc')->get();
        
        // Add debug information to the view
        $debug = [
            'total_drivers' => User::where('role', 'driver')->count(),
            'query_results' => $drivers->count(),
            'initial_count' => $initialCount,
            'filters_applied' => [
                'county_id' => $request->county_id,
                'subcounty' => $request->subcounty,
            ],
        ];
        
        // Pass data to view
        return view('drivers.index', compact('drivers', 'counties', 'request', 'debug'));
    }
}