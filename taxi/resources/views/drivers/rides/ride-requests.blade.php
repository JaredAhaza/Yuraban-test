@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Available Ride Requests</h2>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Customer</th>
                <th>Pickup</th>
                <th>Drop-off</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rides as $ride)
                @if($ride->status === 'pending')
                    <tr>
                        <td>{{ $ride->id }}</td>
                        <td>{{ $ride->customer->name ?? 'N/A' }}</td>
                        <td>{{ $ride->pickup_location }}</td>
                        <td>{{ $ride->dropoff_location }}</td>
                        <td>
                            <form method="POST" action="{{ route('driver.rides.accept', $ride->id) }}">
                                @csrf
                                <button type="submit" class="btn btn-primary">Accept</button>
                            </form>
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</div>
@endsection
