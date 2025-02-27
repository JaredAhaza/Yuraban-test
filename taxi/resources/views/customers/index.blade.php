@extends('layouts.app')

@section('content')
<main class="mt-6">
    <div class="p-4">
        <h2 class="text-2xl font-bold mb-4">Registered Customers</h2>

        <!-- Filtering Form -->
        <form method="GET" action="{{ route('admin.customers.index') }}" class="mb-4">
            <label for="filter_date" class="block text-sm font-medium text-gray-700">Filter by Registration Date:</label>
            <input type="date" id="filter_date" name="filter_date" value="{{ $filterDate }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50" />
            <button type="submit" class="mt-2 bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600">Filter</button>
        </form>

        <!-- Customers Table -->
        <table class="min-w-full border border-gray-300">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border border-gray-300 px-4 py-2">Name</th>
                    <th class="border border-gray-300 px-4 py-2">Phone</th>
                    <th class="border border-gray-300 px-4 py-2">Registration Date</th>
                </tr>
            </thead>
            <tbody>
                @if ($customers->isEmpty())
                    <tr>
                        <td colspan="3" class="border border-gray-300 px-4 py-2 text-center">No customers found.</td>
                    </tr>
                @else
                    @foreach ($customers as $customer)
                        <tr>
                            <td class="border border-gray-300 px-4 py-2">{{ $customer->name }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $customer->phone }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $customer->created_at->format('Y-m-d') }}</td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>

        <!-- Pagination Links -->
        <div class="mt-4">
            {{ $customers->links() }} <!-- Laravel pagination links -->
        </div>
    </div>
</main>
@endsection
