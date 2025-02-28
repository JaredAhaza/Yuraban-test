<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\County;
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
        // Fetch all counties to pass to the view
        $counties = County::all();
        
        return view('auth.register', compact('counties'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:male,female,other'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'regex:/^\+\d{12}$/', 'unique:users,phone'],
            'role' => ['required', 'in:customer,driver,admin'],
            'county_id' => 'required|exists:counties,id',
            'subcounty' => 'required|string|max:255',
            'password' => ['required', 'string', 'digits:4']
        ]);
    
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'gender' => $request->gender,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
            'county_id' => $request->county_id,
            'subcounty' => $request->subcounty,
            'password' => Hash::make($request->password),
        ]);
    
        event(new Registered($user));
        if ($user->role === 'driver' && !$user->is_approved) {
            return redirect()->route('waiting.approval')->with('status', 'Your application is under review.');
        }
    
        return redirect()->route('login')->with('success', 'Account created successfully! Please log in.');
    }
    

    public function storeApi(Request $request)
    {
        // Base validation rules
        $rules = [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:male,female,other'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'regex:/^\+\d{12}$/', 'unique:users,phone'],
            'role' => ['required', 'in:customer,driver,admin'],
            'county_id' => 'required|exists:counties,id',
            'subcounty' => 'required|string|max:255',
            'password' => ['required', 'string', 'digits:4']
        ];
        
        // Add county and subcounty validation for drivers
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'gender' => $request->gender,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
            'county_id' => $request->county_id,
            'subcounty' => $request->subcounty,
            'password' => Hash::make($request->password),
        ]);
        

        event(new Registered($user));

        // Redirect based on role
        if ($user->role === 'driver' && !$user->is_approved) {
            // Redirect to waiting approval page without logging in
            return response()->json([
                'Status' => 'Your application is under review.',
            ], 200);
        } elseif ($user->role !== 'driver') {
            // Redirect to login page for non-driver roles
            return response()->json([
                'Status' => 'Registration successful. Please log in.',
            ], 200);
        }

        // Redirect to login page
        return response()->json([
            'Status' => 'Your account has been created. Please login.',
        ], 200);
    }
}