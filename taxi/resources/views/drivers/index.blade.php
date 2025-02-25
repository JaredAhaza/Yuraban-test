@extends('layouts.app')

@section('content')
<main class="mt-6">
    <div class="p-4">
        <!-- Add Admin Navigation Button (now with red color) -->
        @if(auth()->user()->role === 'admin')
            <div class="mb-4 flex justify-end">
                <a href="{{ route('admin.drivers.index') }}" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                    </svg>
                    Driver Approvals
                </a>
            </div>
        @endif

        <h2 class="text-2xl font-bold mb-4">Registered Drivers</h2>
        
        <!-- County and Sub-County Filter -->
        <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
            <form method="GET" action="{{ route('drivers.index') }}" class="space-y-4 md:space-y-0 md:flex md:space-x-4">
                <div class="md:w-1/3">
                    <label for="county_id" class="block text-sm font-medium text-gray-700">Filter by County</label>
                    <select id="county_id" name="county_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" onchange="updateSubcounties(this.value)">
                        <option value="">All Counties</option>
                        @foreach($counties as $county)
                            <option value="{{ $county->id }}" {{ request('county_id') == $county->id ? 'selected' : '' }}>
                                {{ $county->name ?? $county->county_name ?? 'County #'.$county->id }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="md:w-1/3" id="subcounty-container" style="{{ request('county_id') ? '' : 'display: none;' }}">
                    <label for="subcounty" class="block text-sm font-medium text-gray-700">Filter by Sub-County</label>
                    <select id="subcounty" name="subcounty" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">All Sub-Counties</option>
                        <!-- Subcounties will be loaded dynamically -->
                    </select>
                </div>
                
                <div class="md:w-1/3 flex items-end">
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                        Apply Filter
                    </button>
                    
                    @if(request('county_id') || request('subcounty'))
                        <a href="{{ route('drivers.index') }}" class="ml-2 px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>
        
        <!-- Active Filters Display -->
        @if(request('county_id') || request('subcounty'))
            <div class="mb-4">
                <span class="font-medium">Active filters:</span>
                @if(request('county_id'))
                    @php
                        $county = $counties->firstWhere('id', request('county_id'));
                        $countyName = $county ? ($county->county_name ?? $county->name) : 'Unknown';
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 ml-2">
                        County: {{ $countyName }}
                    </span>
                @endif
                
                @if(request('subcounty'))
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 ml-2">
                        Subcounty: {{ request('subcounty') }}
                    </span>
                @endif
            </div>
        @endif
        
        <!-- Display drivers in a table -->
        <table class="min-w-full border border-gray-300">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border border-gray-300 px-4 py-2">Name</th>
                    <th class="border border-gray-300 px-4 py-2">Phone</th>
                    <th class="border border-gray-300 px-4 py-2">County</th>
                    <th class="border border-gray-300 px-4 py-2">Sub-County</th>
                    <th class="border border-gray-300 px-4 py-2">Status</th>
                    <th class="border border-gray-300 px-4 py-2">Registration Date</th>
                </tr>
            </thead>
            <tbody>
                @if ($drivers->isEmpty())
                    <tr>
                        <td colspan="6" class="border border-gray-300 px-4 py-2 text-center">No drivers found.</td>
                    </tr>
                @else
                    @foreach ($drivers as $driver)
                        <tr>
                            <td class="border border-gray-300 px-4 py-2">{{ $driver->name }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $driver->phone }}</td>
                            <td class="border border-gray-300 px-4 py-2">
                                @if($driver->county)
                                    {{ $driver->county->name ?? $driver->county->county_name ?? 'N/A' }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td class="border border-gray-300 px-4 py-2">{{ $driver->subcounty ?? 'N/A' }}</td>
                            <td class="border border-gray-300 px-4 py-2">
                                @if(isset($driver->is_declined) && $driver->is_declined)
                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Declined</span>
                                @elseif($driver->is_approved)
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Approved</span>
                                @else
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">Pending</span>
                                @endif
                            </td>
                            <td class="border border-gray-300 px-4 py-2">{{ $driver->created_at->format('Y-m-d') }}</td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</main>

<script>
// Store all counties and their subcounties
const counties = @json($counties->map(function($county) {
    return [
        'id' => $county->id,
        'name' => $county->name ?? $county->county_name ?? 'County #' . $county->id,
        'sub_counties' => $county->sub_counties ?? []
    ];
}));

// Function to update subcounties dropdown
function updateSubcounties(countyId) {
    const subcountyContainer = document.getElementById('subcounty-container');
    const subcountySelect = document.getElementById('subcounty');
    
    // Clear current options
    subcountySelect.innerHTML = '<option value="">All Sub-Counties</option>';
    
    if (!countyId) {
        subcountyContainer.style.display = 'none';
        return;
    }
    
    // Find the selected county
    const county = counties.find(c => c.id == countyId);
    if (!county || !county.sub_counties || county.sub_counties.length === 0) {
        subcountyContainer.style.display = 'none';
        return;
    }
    
    // Add subcounty options
    county.sub_counties.forEach(subcounty => {
        const option = document.createElement('option');
        option.value = subcounty;
        option.textContent = subcounty;
        option.selected = "{{ request('subcounty') }}" === subcounty;
        subcountySelect.appendChild(option);
    });
    
    // Show the subcounty dropdown
    subcountyContainer.style.display = 'block';
}

// Initialize subcounties on page load if county is selected
document.addEventListener('DOMContentLoaded', function() {
    const countySelect = document.getElementById('county_id');
    if (countySelect.value) {
        updateSubcounties(countySelect.value);
    }
});
</script>
@endsection