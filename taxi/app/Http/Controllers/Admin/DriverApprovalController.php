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
        if (!Auth::user()->isAdmin()) {
        //    return response()->json(['message' => 'Unauthorized','User'=> Auth::user(), 'isAdmin' => Auth::user()->isAdmin()]);
          return redirect()->route('home'); // Redirect if not an admin
        }

        $drivers = User::where('role', 'driver')->where('is_approved', false)->get();
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
}

class DriverDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        //Check if the user is a driver is approved
        if ($user->role !== 'driver' || !$user->is_approved) {
            return redirect()->route('waiting.approval');// Redirect to waiting approval if not approved yet
        }

        //show the driver's dashboard
        return view('driver.dashboard');
    }
}
