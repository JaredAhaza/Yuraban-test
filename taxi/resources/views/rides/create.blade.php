@extends('layouts.app')

@section('content')
<main class="mt-6">
    <div class="p-4">
        <h2 class="text-2xl font-bold mb-4">Book a Ride</h2>
        <form method="POST" action="{{ route('ride.store') }}" class="space-y-4">
            @csrf
            <div>
                <label for="pickup" class="block text-sm font-medium text-gray-700">Pickup Location</label>
                <input type="text" id="pickup" name="pickup" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50" />
            </div>
            <div>
                <label for="dropoff" class="block text-sm font-medium text-gray-700">Dropoff Location</label>
                <input type="text" id="dropoff" name="dropoff" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50" />
            </div>
            <div>
                <label for="time" class="block text-sm font-medium text-gray-700">Pickup Time</label>
                <input type="datetime-local" id="time" name="time" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50" />
            </div>
            <div>
                <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded-md hover:bg-blue-600">Book Ride</button>
            </div>
        </form>
    </div>
</main>
@endsection
