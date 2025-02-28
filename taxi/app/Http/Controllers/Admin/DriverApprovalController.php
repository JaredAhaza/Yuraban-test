<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class DriverApprovalController extends Controller
{
    /**
     * Displaying a list of drivers awaiting approval
     */
    public function index()
    {
        $user = Auth::user();
    
        // Check if the user's role is NOT 'admin'
        if ($user->role !== 'admin') {
            \Log::error('Unauthorized access attempt to driver approval page by user ID: ' . $user->id);
            return redirect()->route('home'); // Redirect if not an admin
        }
    
        // Fetch drivers awaiting approval
        $drivers = User::where('is_approved', false)
                       ->where('is_declined', false) // Exclude declined drivers
                       ->get();
    
        // Log the drivers being fetched
        \Log::info('Drivers awaiting approval:', $drivers->toArray());
    
        return view('admin.driver-approval', compact('drivers'));
    }
    
    /**
     * Approve specified driver
     */
    public function approve($id)
    {
        $driver = User::findOrFail($id);
        $driver->is_approved = true;
        $driver->save();

        return redirect()->back()->with('status', 'Driver approved successfully');
    }

    /**
     * Decline approval of specified driver
     */
    public function decline($id)
    {
        $driver = User::findOrFail($id);
        $driver->is_approved = false;
        $driver->is_declined = true;
        $driver->save();

        return redirect()->route('admin.drivers.index')->with('status', 'Driver application declined successfully.');
    }
}

class DriverDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        //Check if the user is a driver is approved or declined
        $drivers = User::where('role', 'driver')->where('is_approved', true)->where('is_declined', false)->get();
        return view('admin.driver-dashboard', compact('drivers'));

        //show the driver's dashboard
        return view('driver.dashboard');
    }
}
