@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mt-4 mb-4">My Ride History</h2>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-header">
            <h5>Your Rides</h5>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Pickup Location</th>
                        <th>Destination</th>
                        <th>Driver</th>
                        <th>Status</th>
                        <th>Requested At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rides as $ride)
                        <tr>
                            <td>{{ $ride->id }}</td>
                            <td>{{ $ride->pickup_location }}</td>
                            <td>{{ $ride->destination }}</td>
                            <td>
                                @if($ride->driver)
                                    {{ $ride->driver->name }}
                                @else
                                    <span class="text-muted">Not Assigned</span>
                                @endif
                            </td>
                            <td>
                                @if($ride->status == 'pending')
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @elseif($ride->status == 'accepted')
                                    <span class="badge bg-info">Accepted</span>
                                @elseif($ride->status == 'in_progress')
                                    <span class="badge bg-primary">In Progress</span>
                                @elseif($ride->status == 'completed')
                                    <span class="badge bg-success">Completed</span>
                                @elseif($ride->status == 'cancelled')
                                    <span class="badge bg-danger">Cancelled</span>
                                @endif
                            </td>
                            <td>{{ $ride->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <a href="{{ route('customer.rides.show', $ride->id) }}" class="btn btn-primary btn-sm">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">You have no rides yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
    </div>
</div>
@endsection
