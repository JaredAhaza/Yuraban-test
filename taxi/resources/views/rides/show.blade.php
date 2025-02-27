@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Ride Details</h2>
    
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Ride ID: {{ $ride->id }}</h5>
            
            <p><strong>Pickup Location:</strong> {{ $ride->pickup_location }}</p>
            <p><strong>Destination:</strong> {{ $ride->destination }}</p>
            <p><strong>Pickup Coordinates:</strong> {{ $ride->pickup_coordinates }}</p>
            <p><strong>Destination Coordinates:</strong> {{ $ride->dropoff_coordinates }}</p>
            <p><strong>Distance:</strong> {{ number_format($ride->distance, 2) }} km</p>
            <p><strong>Fare Amount:</strong> KES {{ number_format($ride->fare_amount, 2) }}</p>
            <p><strong>Status:</strong> 
                <span class="badge 
                    @if($ride->status == 'pending') bg-warning 
                    @elseif($ride->status == 'accepted') bg-primary 
                    @elseif($ride->status == 'completed') bg-success 
                    @elseif($ride->status == 'cancelled') bg-danger 
                    @endif">
                    {{ ucfirst($ride->status) }}
                </span>
            </p>
            
            @if($ride->status == 'pending' || $ride->status == 'accepted')
            <form action="{{ route('customer.rides.cancel', $ride->id) }}" method="POST">
                @csrf
                <input name="cancellation_reason" value="Customer requested cancellation">
                <button type="submit" class="btn btn-danger">Cancel Ride</button>
            </form>
            @endif
        </div>
    </div>

    <a href="{{ route('customer.rides.index') }}" class="btn btn-secondary mt-3">Back to Rides</a>
</div>
@endsection
