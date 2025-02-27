@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <h3 class="text-lg font-medium">{{ __('Edit Profile') }}</h3>

                @if (session('status') === 'profile-updated')
                    <div class="alert alert-success mt-4">
                        {{ __('Your profile has been updated.') }}
                    </div>
                @endif

                <!-- Profile Update Form -->
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PUT')

                    <!-- Name -->
                    <div class="mt-4">
                        <label for="name" class="block text-sm font-medium">First Name:</label>
                        <input type="text" id="first_name" name="first_name" value="{{ old('first_name', auth()->user()->first_name) }}" class="w-full px-3 py-2 border rounded-md dark:bg-gray-700">
                    </div>

                    <div class="mt-4">
                        <label for="name" class="block text-sm font-medium">Last Name:</label>
                        <input type="text" id="last_name" name="last_name" value="{{ old('last_name', auth()->user()->last_name) }}" class="w-full px-3 py-2 border rounded-md dark:bg-gray-700">
                    </div>

                    <!-- Email -->
                    <div class="mt-4">
                        <label for="email" class="block text-sm font-medium">Email:</label>
                        <input type="email" id="email" name="email" value="{{ old('email', auth()->user()->email) }}" 
                               class="w-full px-3 py-2 border rounded-md dark:bg-gray-700">
                    </div>

                    <!-- Phone -->
                    <div class="mt-4">
                        <label for="phone" class="block text-sm font-medium">Phone:</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone', auth()->user()->phone) }}" 
                               class="w-full px-3 py-2 border rounded-md dark:bg-gray-700">
                    </div>


                    <!-- Submit Button -->
                    <div class="mt-6">
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
