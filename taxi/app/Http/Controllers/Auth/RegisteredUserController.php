<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^\+\d{12}$/', 'unique:users,phone'],
            'role' => ['required', 'in:customer,driver,admin'], // Include admin in the validation
            'password' => ['required', 'string', 'digits:4'], // Ensure the password is a 4-digit PIN
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Create the user
        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'role' => $request->role,
            'is_approved' => $request->role === 'driver' ? false : true, // Admins are automatically approved
            'is_admin' => $request->role === 'admin', // Set is_admin based on the role
            'password' => Hash::make($request->password),
        ]);

        // Redirect based on role
        if ($user->role === 'driver' && !$user->is_approved) {
            // Redirect to waiting approval page without logging in
            return redirect()->route('waiting.approval')->with('status', 'Your application is under review.');
        } elseif ($user->role !== 'driver') {
            // Redirect to login page for non-driver roles
            return redirect()->route('login')->with('status', 'Registration successful. Please log in.');
        }

        //Redirect to login page
        return redirect()->route('login')->with('status', 'Your account has been created. Please login.');
    }
}
