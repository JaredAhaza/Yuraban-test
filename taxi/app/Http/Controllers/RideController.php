<?php

namespace App\Http\Controllers;

use App\Models\Ride; // Import the Ride model
use Illuminate\Http\Request;

class RideController extends Controller
{
    //function to create rides
    public function create()
    {
        return view('rides.create');// view for booking ride
    }

    public function store(Request $request)
    {
        // Validate and process the booking
        $request->validate([
            'pickup' => 'required|string',
            'dropoff' => 'required|string',
            'time' => 'required|date',
        ]);

        // Calculate price based on pickup and dropoff locations
        $price = $this->calculatePrice($request->pickup, $request->dropoff);

        // Store the ride in the database
        $ride = new Ride();
        $ride->pickup = $request->pickup;
        $ride->dropoff = $request->dropoff;
        $ride->time = $request->time;
        $ride->price = $price; // Set the calculated price
        $ride->save(); // Save the ride to the database

        return redirect()->route('ride.requests')->with('success', "Ride booked successfully! Price: $price");
    }

    public function requests()
    {
        // Fetch ride requests from the database
        $rides = Ride::orderBy('created_at', 'desc')->get(); // Get all rides, latest first

        return view('rides.requests', compact('rides')); // Pass rides to the view
    }

    private function calculatePrice($pickup, $dropoff)
    {
        // Placeholder for price calculation logic
        return rand(100, 1000); // Random price for demonstration
    }
}
