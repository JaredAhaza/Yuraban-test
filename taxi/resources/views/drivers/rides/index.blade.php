@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mt-4 mb-4">Driver Dashboard</h2>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-4">
        <div class="card-header">
            <h5>Available Rides</h5>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Phone Number</th>
                        <th>Pickup Location</th>
                        <th>Destination</th>
                        <th>No of Passangers</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($availableRides as $ride)
                        <tr>
                            <td>{{ $ride->customer->first_name }}</td>
                            <td>{{ $ride->customer->last_name }}</td>
                            <td>{{ $ride->customer->phone }}</td>
                            <td>{{ $ride->pickup_location }}</td>
                            <td>{{ $ride->destination }}</td>
                            <td>{{ $ride->passengers }}</td>
                            <td>
                                <form action="{{ route('driver.rides.accept', $ride->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">Accept</button>
                                </form>
                                <form action="{{ route('driver.rides.decline', $ride->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm">Decline</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No available rides.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5>My Assigned Rides</h5>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Pickup Location</th>
                        <th>Customer Phone</th>
                        <th>Destination</th>
                        <th>Status</th>
                        <th>Requested At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($myRides as $ride)
                        <tr>
                            <td>{{ $ride->id }}</td>
                            <td>{{ $ride->pickup_location }}</td>
                            <td>
                                @if($ride->customer)
                                    {{ $ride->customer->phone }}
                                @else
                                    <span class="text-muted">Not Assigned</span>
                                @endif
                            </td>
                            <td>{{ $ride->destination }}</td>
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
                                @if($ride->status === 'pending' && $ride->driver_id === null)
                                    <form action="{{ route('driver.rides.accept', $ride->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm">Accept</button>
                                    </form>
                                @elseif($ride->status === 'accepted' && $ride->driver_id === auth()->id())
                                    <form action="{{ route('driver.rides.start', $ride->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-warning btn-sm">Start</button>
                                    </form>
                                @elseif($ride->status === 'in_progress' && $ride->driver_id === auth()->id())
                                    <form action="{{ route('driver.rides.complete', $ride->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-info btn-sm">Complete</button>
                                    </form>
                                @endif

                                @if(in_array($ride->status, ['accepted', 'in_progress']) && $ride->driver_id === auth()->id())
                                    <form action="{{ route('driver.rides.cancel', $ride->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm">Cancel</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">You have no assigned rides.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card mb-4">
    <div class="card-header">
        <h5>Declined Rides</h5>
    </div>
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Pickup Location</th>
                    <th>Destination</th>
                    <th>Requested At</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($declinedRides as $ride)
                    <tr>
                        <td>{{ $ride->id }}</td>
                        <td>{{ $ride->pickup_location }}</td>
                        <td>{{ $ride->destination }}</td>
                        <td>{{ $ride->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">No declined rides.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="text-center mt-4">
    <form action="{{ route('driver.toggleOnline') }}" method="POST" class="d-inline">
        @csrf
        @if(auth()->user()->is_online)
            <button type="submit" class="btn btn-danger">Go Offline</button>
        @else
            <button type="submit" class="btn btn-success">Go Online</button>
        @endif
    </form>
</div>

</div>
@endsection
