@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">All Ride Requests</h2>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <table class="table table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Customer</th>
                <th>Driver</th>
                <th>Pickup</th>
                <th>Drop-off</th>
                <th>Status</th>
                <th>Requested At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rides as $ride)
                <tr>
                    <td>{{ $ride->id }}</td>
                    <td>{{ $ride->customer->name ?? 'N/A' }}</td>
                    <td>{{ $ride->driver->name ?? 'Unassigned' }}</td>
                    <td>{{ $ride->pickup_location }}</td>
                    <td>{{ $ride->destination }}</td>
                    <td><strong>{{ ucfirst($ride->status) }}</strong></td>
                    <td>{{ $ride->created_at->format('d M Y, H:i A') }}</td>
                    <td>

                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
