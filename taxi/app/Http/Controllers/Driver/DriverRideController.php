<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\Ride;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class DriverRideController extends Controller
{
    /**
     * Display a list of available and assigned rides for the driver.
     */
    public function index()
    {
        $user = Auth::user();
        $userId = $user->id;
    
        // Check if the driver is online before showing available rides
        $availableRides = collect(); // Default to an empty collection
        if ($user->is_online) {
            $availableRides = Ride::where('status', 'pending')
                ->whereNull('driver_id')
                ->where(function ($query) use ($userId) {
                    $query->whereNull('declined_by')
                        ->orWhereRaw("NOT JSON_CONTAINS(declined_by, ?)", [$userId]);
                })
                ->latest()
                ->get();
        }
    
        // Get rides assigned to this driver
        $myRides = $user->ridesAsDriver()->latest()->get();
    
        // Get rides that this driver has declined
        $declinedRides = Ride::whereNotNull('declined_by')
            ->whereRaw("JSON_CONTAINS(declined_by, ?)", [$userId])
            ->latest()
            ->get();
    
        return view('drivers.rides.index', compact('availableRides', 'myRides', 'declinedRides'));
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
     * Decline a ride request.
     */
    public function decline(Ride $ride)
    {
        if ($ride->status !== 'pending' || $ride->driver_id !== null) {
            return back()->with('error', 'This ride is no longer available.');
        }
    
        $userId = Auth::id();
        Log::info("Driver {$userId} is trying to decline ride ID {$ride->id}");
    
        // Ensure `declined_by` is always stored as an array
        $declinedDrivers = $ride->declined_by ? json_decode($ride->declined_by, true) : [];
    
        Log::info("Current declined_by before update: ", ['declined_by' => $declinedDrivers]);
    
        // If driver hasn't already declined, add them
        if (!in_array($userId, $declinedDrivers)) {
            $declinedDrivers[] = $userId;
        }
    
        Log::info("Updated declined_by: ", ['declined_by' => $declinedDrivers]);
    
        // Update only `declined_by`, keep `status` as pending
        $ride->update([
            'declined_by' => json_encode($declinedDrivers),
            'declined_at' => now(),
        ]);
    
        Log::info("Ride {$ride->id} declined successfully by driver {$userId}");
    
        return redirect()->route('driver.rides.index')
            ->with('success', 'Ride declined successfully.');
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

    public function toggleOnline()
    {
    $user = Auth::user();
    $user->is_online = !$user->is_online;
    $user->save();

    $status = $user->is_online ? 'Online' : 'Offline';
    return back()->with('success', "You are now {$status}.");
    }

    public function indexapi()
    {
        $user = Auth::user();
        $userId = $user->id;

        if (!$user->is_online) {
            return response()->json([
                'message' => 'You are offline. Please go online to see available rides.',
                'available_rides' => [],
                'my_rides' => [],
                'declined_rides' => []
            ], 200);
        }

        $availableRides = Ride::where('status', 'pending')
            ->whereNull('driver_id')
            ->where(function ($query) use ($userId) {
                $query->whereNull('declined_by')
                    ->orWhereRaw("NOT JSON_CONTAINS(declined_by, ?)", [$userId]);
            })
            ->latest()
            ->get();

        $myRides = $user->ridesAsDriver()->latest()->get();
        $declinedRides = Ride::whereNotNull('declined_by')
            ->whereRaw("JSON_CONTAINS(declined_by, ?)", [$userId])
            ->latest()
            ->get();

        return response()->json([
            'available_rides' => $availableRides,
            'my_rides' => $myRides,
            'declined_rides' => $declinedRides
        ], 200);
    }

    public function acceptapi(Ride $ride)
    {
        if ($ride->status !== 'pending' || $ride->driver_id !== null) {
            return response()->json(['error' => 'This ride is no longer available.'], 400);
        }

        if (!Auth::user()->is_approved) {
            return response()->json(['error' => 'You need to be approved to accept rides.'], 403);
        }

        $ride->update([
            'driver_id' => Auth::id(),
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        return response()->json(['message' => 'Ride accepted. Proceed to pickup.'], 200);
    }

    public function declineapi(Ride $ride)
    {
        if ($ride->status !== 'pending' || $ride->driver_id !== null) {
            return response()->json(['error' => 'This ride is no longer available.'], 400);
        }

        $userId = Auth::id();
        $declinedDrivers = $ride->declined_by ? json_decode($ride->declined_by, true) : [];

        if (!in_array($userId, $declinedDrivers)) {
            $declinedDrivers[] = $userId;
        }

        $ride->update([
            'declined_by' => json_encode($declinedDrivers),
            'declined_at' => now(),
        ]);

        return response()->json(['message' => 'Ride declined successfully.'], 200);
    }

    public function startapi(Ride $ride)
    {
        if ($ride->driver_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        if ($ride->status !== 'accepted') {
            return response()->json(['error' => 'This ride cannot be started.'], 400);
        }

        $ride->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        return response()->json(['message' => 'Ride started. Safe journey!'], 200);
    }

    public function completeapi(Ride $ride)
    {
        if ($ride->driver_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        if ($ride->status !== 'in_progress') {
            return response()->json(['error' => 'This ride cannot be completed.'], 400);
        }

        $ride->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return response()->json(['message' => 'Ride completed successfully.'], 200);
    }


    public function cancelapi(Ride $ride, Request $request)
    {
        if ($ride->driver_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        if (!in_array($ride->status, ['accepted', 'in_progress'])) {
            return response()->json(['error' => 'This ride cannot be cancelled.'], 400);
        }

        $validated = $request->validate([
            'cancellation_reason' => 'required|string|max:255',
        ]);

        $ride->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $validated['cancellation_reason'],
        ]);

        return response()->json(['message' => 'Ride cancelled.'], 200);
    }


    public function toggleOnlineapi()
    {
        $user = Auth::user();
        $user->is_online = !$user->is_online;
        $user->save();

        $status = $user->is_online ? 'Online' : 'Offline';
        return response()->json(['message' => "You are now {$status}."], 200);
    }


}
