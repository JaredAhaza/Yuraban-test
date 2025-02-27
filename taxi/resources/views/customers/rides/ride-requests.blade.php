@extends('layouts.app')

@section('content')
<div class="container">
    <h2>My Ride Requests</h2>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
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
                    <td>{{ $ride->pickup_location }}</td>
                    <td>{{ $ride->dropoff_location }}</td>
                    <td>{{ ucfirst($ride->status) }}</td>
                    <td>{{ $ride->created_at->format('d M Y, H:i A') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
