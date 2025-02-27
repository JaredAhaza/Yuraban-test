<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ride;
use App\Models\User;
use Illuminate\Http\Request;

class AdminRideController extends Controller
{
        /**
     * Display all ride requests (pending rides).
     */
    public function rideRequests()
    {
        $rides = Ride::latest()->paginate(10); // Fetches all rides
    
        return view('admin.rides.requests', compact('rides'));
    }
    
    
    /** 
     * Display a list of all rides with filtering options.
     */
    public function index(Request $request)
    {
        $status = $request->input('status');  // Optional filter by status
        $ridesQuery = Ride::with(['customer', 'driver']); // Load relationships

        if ($status) {
            $ridesQuery->where('status', $status);
        }

        $rides = $ridesQuery->latest()->paginate(10); // Paginated for better performance

        return view('admin.rides.index', compact('rides', 'status'));
    }

    /**
     * Show ride details.
     */
    public function show(Ride $ride)
    {
        return view('admin.rides.show', compact('ride'));
    }

    /**
     * Assign a driver to a ride manually.
     */
    public function assignDriver(Request $request, Ride $ride)
    {
        $request->validate([
            'driver_id' => 'required|exists:users,id',
        ]);

        $driver = User::findOrFail($request->driver_id);

        // Ensure driver is valid and approved
        if (!$driver->is_driver || !$driver->is_approved) {
            return back()->with('error', 'Selected user is not an approved driver.');
        }

        // Prevent reassignment
        if ($ride->driver_id) {
            return back()->with('error', 'This ride already has a driver assigned.');
        }

        // Assign driver
        $ride->update([
            'driver_id' => $driver->id,
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        return back()->with('success', 'Driver assigned successfully.');
    }

    /**
     * Manually update the status of a ride.
     */
    public function updateStatus(Request $request, Ride $ride)
    {
        $request->validate([
            'status' => 'required|in:pending,accepted,in_progress,completed,cancelled',
        ]);

        $newStatus = $request->status;

        if ($newStatus === 'cancelled') {
            return back()->with('error', 'Use the cancel method to cancel rides.');
        }

        // Set timestamps based on status
        $timestamps = [
            'accepted' => 'accepted_at',
            'in_progress' => 'started_at',
            'completed' => 'completed_at',
        ];

        $updateData = ['status' => $newStatus];

        if (isset($timestamps[$newStatus])) {
            $updateData[$timestamps[$newStatus]] = now();
        }

        $ride->update($updateData);

        return back()->with('success', 'Ride status updated successfully.');
    }

    /**
     * Cancel a ride with a reason.
     */
    public function cancel(Request $request, Ride $ride)
    {
        $request->validate([
            'cancellation_reason' => 'required|string|max:255',
        ]);

        if ($ride->status === 'completed') {
            return back()->with('error', 'Cannot cancel a completed ride.');
        }

        $ride->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $request->cancellation_reason,
        ]);

        return back()->with('success', 'Ride has been cancelled.');
    }
}
