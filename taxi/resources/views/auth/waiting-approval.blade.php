<x-guest-layout>
    <div class="text-center">
        <h1>Waiting for Approval</h1>
        <p>Your application to become a driver is under review. You will be notified once your account is approved</p>
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <a href="{{ route('login') }}" class="btn btn-primary">Return to Login</a>
    </div>
</x-guest-layout>