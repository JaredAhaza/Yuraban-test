<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            // Authentication passed...
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard', absolute: false));
        }

        // If authentication fails, redirect back with an error
        return back()->withErrors([
            'phone' => 'The provided credentials do not match our records.',
        ]);
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
