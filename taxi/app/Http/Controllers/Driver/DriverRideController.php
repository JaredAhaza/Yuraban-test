<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\Ride;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DriverRideController extends Controller
{
    /**
     * Display a list of available and assigned rides for the driver.
     */
    public function index()
    {
        $availableRides = Ride::where('status', 'pending')
                            ->whereNull('driver_id')
                            ->latest()
                            ->get();

        $myRides = Auth::user()->ridesAsDriver()
                    ->latest()
                    ->get();

        return view('drivers.rides.index', compact('availableRides', 'myRides'));
    }

    /**
     * Accept a ride request.
     */
    public function accept(Ride $ride)
    {
        if ($ride->status !== 'pending' || $ride->driver_id !== null) {
            return back()->with('error', 'This ride is no longer available.');
        }

        if (!Auth::user()->is_approved) {
            return back()->with('error', 'You need to be approved to accept rides.');
        }

        $ride->update([
            'driver_id' => Auth::id(),
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        // Stay on the ride list after accepting
        return redirect()->route('driver.rides.index')
            ->with('success', 'Ride accepted. Proceed to the pickup location.');
    }

    /**
     * Start a ride.
     */
    public function start(Ride $ride)
    {
        if ($ride->driver_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($ride->status !== 'accepted') {
            return back()->with('error', 'This ride cannot be started.');
        }

        $ride->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        return back()->with('success', 'Ride started. Safe journey!');
    }

    /**
     * Complete a ride.
     */
    public function complete(Ride $ride)
    {
        if ($ride->driver_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($ride->status !== 'in_progress') {
            return back()->with('error', 'This ride cannot be completed.');
        }

        $ride->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return redirect()->route('driver.rides.index')
            ->with('success', 'Ride completed successfully.');
    }

    /**
     * Cancel a ride.
     */
    public function cancel(Ride $ride, Request $request)
    {
        if ($ride->driver_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if (!in_array($ride->status, ['accepted', 'in_progress'])) {
            return back()->with('error', 'This ride cannot be cancelled.');
        }

        $validated = $request->validate([
            'cancellation_reason' => 'required|string|max:255',
        ]);

        $ride->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $validated['cancellation_reason'],
        ]);

        return redirect()->route('driver.rides.index')
            ->with('success', 'Ride cancelled.');
    }
}
