@extends('layouts.app')

@section('content')
<main class="mt-6">
    <div class="p-4">
        <h2 class="text-2xl font-bold mb-4">Registered Drivers</h2>
        <!-- Add filtering options for counties and sub-counties here -->
        <!-- Display drivers in a table -->
        <table class="min-w-full border border-gray-300">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border border-gray-300 px-4 py-2">Name</th>
                    <th class="border border-gray-300 px-4 py-2">Phone</th>
                    <th class="border border-gray-300 px-4 py-2">Registration Date</th>
                </tr>
            </thead>
            <tbody>
                @if ($drivers->isEmpty())
                    <tr>
                        <td colspan="3" class="border border-gray-300 px-4 py-2 text-center">No drivers found.</td>
                    </tr>
                @else
                    @foreach ($drivers as $driver)
                        <tr>
                            <td class="border border-gray-300 px-4 py-2">{{ $driver->name }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $driver->phone }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $driver->created_at->format('Y-m-d') }}</td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</main>
@endsection
