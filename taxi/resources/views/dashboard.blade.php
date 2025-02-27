@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <h3 class="text-lg font-medium">{{ __('User Profile') }}</h3>

                <!-- Success Message -->
                @if (session('status') === 'profile-updated')
                    <div class="alert alert-success mt-4">
                        {{ __('Your profile has been updated.') }}
                    </div>
                @endif

                <!-- User Profile Details -->
                <div class="mt-6">
                    <p><strong>Name:</strong> {{ auth()->user()->first_name }} {{ auth()->user()->last_name }} </p>
                    <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
                    <p><strong>Phone:</strong> {{ auth()->user()->phone ?? 'Not Provided' }}</p>
                    <p><strong>Role:</strong> {{ ucfirst(auth()->user()->role) }}</p>
                </div>

                <!-- Edit Profile Button -->
                <a href="{{ route('profile.edit') }}" class="btn btn-primary mt-4">Edit Profile</a>
            </div>
        </div>
    </div>
</div>
@endsection
