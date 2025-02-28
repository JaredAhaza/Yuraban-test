<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Validate the phone and 4-digit PIN
        $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string|digits:4', // Ensure the password is a 4-digit PIN
        ]);

        // Attempt to authenticate the user
        if (Auth::attempt(['phone' => $request->phone, 'password' => $request->password])) {
            // Authentication passed, check if the user is declined
            $user = Auth::user();
            
            // Check if the user is declined
            if ($user->is_declined) {
                // If the user is declined, log them out and redirect to the declined view
                Auth::logout();
                return redirect()->route('declined')->with('error', 'Your application has been denied. Please try again after 3 months.');
            }

            // Check if the user is a driver and not approved
            if ($user->role === 'driver' && !$user->is_approved) {
                // If the user is not approved, log them out and redirect to waiting approval
                Auth::logout();
                return redirect()->route('waiting.approval')->with('error', 'Your account is not approved yet.');
            }

            // Regenerate session and redirect to intended page
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        // If authentication fails, redirect back with an error
        return back()->withErrors([
            'phone' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Customer Login API
     */
    public function customerLogin(Request $request)
    {
        // Validate request
        $rules->validate([
            'phone' => 'required|string',
            'password' => 'required|string|digits:4',
        ]);

        // Attempt to find user
        $user = User::where('phone', $request->phone)
                    ->where('role', 'customer')
                    ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Create API Token
        $token = $user->createToken('customer-token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token
        ], 200);
    }

    /**
     * Driver Login API
     */
    public function driverLogin(Request $request)
    {
        // Validate request
        $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string|digits:4',
        ]);

        // Attempt to find user
        $user = User::where('phone', $request->phone)
                    ->where('role', 'driver')
                    ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Check if driver is approved
        if (!$user->is_approved) {
            return response()->json([
                'status' => 'error',
                'message' => 'Your account is not approved yet. Please wait for admin approval.'
            ], 403);
        }

        // Create API Token
        $token = $user->createToken('driver-token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token
        ], 200);
    }

    /**
     * Logout API
     */
    public function logoutApi(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully'
        ], 200);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
