@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Edit Ride Request</h2>

    <form action="{{ route('ride.update', $ride->id) }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="status">Ride Status</label>
            <select name="status" id="status" class="form-control">
                <option value="pending" {{ $ride->status == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ $ride->status == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="completed" {{ $ride->status == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ $ride->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>

        <div class="form-group">
            <label for="driver_id">Assign Driver</label>
            <select name="driver_id" id="driver_id" class="form-control">
                <option value="">-- Select a Driver --</option>
                @foreach($drivers as $driver)
                    <option value="{{ $driver->id }}" {{ $ride->driver_id == $driver->id ? 'selected' : '' }}>
                        {{ $driver->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-success">Update Ride</button>
        <a href="{{ route('ride.requests') }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection
