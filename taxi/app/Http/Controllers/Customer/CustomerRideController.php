<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Ride;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\RideRequested;

class CustomerRideController extends Controller
{
    /**
     * Display a list of customer rides.
     */
    public function index()
    {
        $rides = Auth::user()->ridesAsCustomer()->latest()->get();
        return view('customers.rides.index', compact('rides'));
    }

    /**
     * Show the form to request a ride.
     */
    public function create()
    {
        return view('rides.create');
    }

    /**
     * Store a newly created ride request.
     */
    public function store(Request $request)
    {
        \Log::info('Ride Request Data:', $request->all()); // Log request data

        // Validate data
        $validated = $request->validate([
            'pickup_location' => 'required|string|max:255',
            'pickup_coordinates' => 'required|string',
            'destination' => 'required|string|max:255',
            'destination_coordinates' => 'required|string',
            'passengers' => 'required|integer|min:1|max:6', // Add validation for passengers
        ]);

        // Check if there are any online drivers
        $onlineDrivers = User::where('role', 'driver')
        ->where('is_online', true)
        ->exists();
    

        if (!$onlineDrivers) {
            return redirect()->back()->with('error', 'No drivers are online at the moment. Please try again later.');
        }
        

        // Auto-set scheduled_at to current time
        $validated['scheduled_at'] = now();

        $ride = new Ride();
        $ride->pickup_location = $validated['pickup_location'];
        $ride->destination = $validated['destination']; // Corrected field
        $ride->pickup_coordinates = $validated['pickup_coordinates'];
        $ride->destination_coordinates = $validated['destination_coordinates']; // Corrected field
        $ride->customer_id = Auth::id();
        $ride->status = 'pending';
        $ride->passengers = $validated['passengers'];

        // Calculate distance and fare
        $ride->distance = $this->calculateDistance($ride->pickup_coordinates, $ride->destination_coordinates);
        $ride->fare_amount = $this->calculateFare($ride->distance, $ride->passengers);

        $ride->save();

        event(new RideRequested($ride));

        return redirect()->route('customer.rides.show', $ride)
            ->with('success', 'Ride request created successfully. Waiting for a driver to accept.');
    }

    /**
     * Show the ride details.
     */
    public function show($id)
    {
        $ride = Ride::findOrFail($id); // Ensure the ride exists
    
        if ($ride->customer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
    
        return view('rides.show', compact('ride'));
    }
    

    /**
     * Cancel a ride.
     */
    public function cancel(Ride $ride, Request $request)
    {
        if ($ride->customer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if (!in_array($ride->status, ['pending', 'accepted'])) {
            return back()->with('error', 'This ride cannot be cancelled anymore.');
        }

        $validated = $request->validate([
            'cancellation_reason' => 'required|string|max:255',
        ]);

        $ride->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $validated['cancellation_reason'],
        ]);

        return redirect()->route('customer.rides.index')
            ->with('success', 'Ride cancelled successfully.');
    }

    /**
     * Display a list of pending ride requests.
     */
    public function requests()
    {
        $rides = Ride::where('status', 'pending')->latest()->get();
        return view('rides.requests', compact('rides'));
    }

    /**
     * Calculate the distance between pickup and destination using Haversine formula.
     */
    private function calculateDistance($pickupCoordinates, $destinationCoordinates)
    {
        list($pickupLat, $pickupLng) = explode(',', $pickupCoordinates);
        list($destinationLat, $destinationLng) = explode(',', $destinationCoordinates);

        $pickupLat = deg2rad($pickupLat);
        $pickupLng = deg2rad($pickupLng);
        $destinationLat = deg2rad($destinationLat);
        $destinationLng = deg2rad($destinationLng);

        $earthRadius = 6371; // Earth radius in kilometers

        $deltaLat = $destinationLat - $pickupLat;
        $deltaLng = $destinationLng - $pickupLng;

        $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
             cos($pickupLat) * cos($destinationLat) *
             sin($deltaLng / 2) * sin($deltaLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c; // Distance in kilometers
    }

    /**
     * Calculate the ride fare based on distance.
     */
    private function calculateFare($distance, $passengers)
    {
        $farePerKm = 5; // Base fare per km
        $passengerSurcharge = 2; // Extra charge per additional passenger
    
        // Base fare calculation
        $fare = ceil($distance) * $farePerKm;
    
        // Add passenger surcharge for extra passengers (more than 1)
        if ($passengers > 1) {
            $fare += ($passengers - 1) * $passengerSurcharge;
        }
    
        return $fare;
    }
    
}
