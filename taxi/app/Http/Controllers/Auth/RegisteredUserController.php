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
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15|unique:users,phone',
            'role' => 'required|in:customer,driver',
            'county_id' => 'required|exists:counties,id',
            'subcounty' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);
    
        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'role' => $request->role,
            'county_id' => $request->county_id,
            'subcounty' => $request->subcounty,
            'password' => Hash::make($request->password),
        ]);
    
        event(new Registered($user));
    
        return redirect()->route('login')->with('success', 'Account created successfully! Please log in.');
    }
    

    public function storeApi(Request $request)
    {
        // Base validation rules
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^\+\d{12}$/', 'unique:users,phone'],
            'role' => ['required', 'in:customer,driver,admin'],
            'password' => ['required', 'string', 'digits:4'],
        ];
        
        // Add county and subcounty validation for drivers
        if ($request->role === 'driver') {
            $rules['county_id'] = ['required', 'exists:counties,id'];
            $rules['subcounty'] = ['required', 'string'];
            
            // Validate that the subcounty exists in the selected county's sub_counties array
            $validator = Validator::make($request->all(), $rules);
            
            $validator->after(function ($validator) use ($request) {
                $county = County::find($request->county_id);
                if ($county && !in_array($request->subcounty, $county->sub_counties)) {
                    $validator->errors()->add('subcounty', 'The selected subcounty is invalid.');
                }
            });
            
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'The given data was invalid.',
                    'errors' => $validator->errors(),
                ], 422);
            }
        } else {
            // For non-drivers, just validate the base rules
            $validator = Validator::make($request->all(), $rules);
            
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'The given data was invalid.',
                    'errors' => $validator->errors(),
                ], 422);
            }
        }

        // Check if the phone number is already registered
        $existingUser = User::where('phone', $request->phone)->first();
        if ($existingUser) {
            // Check if the user is declined
            if ($existingUser->is_declined) {
                return response()->json([
                    'message' => 'The given data was invalid.',
                    'phone' => 'Your application has been denied. Please try again after 3 months.',
                ], 422);
            }

            // Check if the user is approved
            if ($existingUser->is_approved) {
                return response()->json([
                    'phone' => 'You are already registered and approved.',
                ], 200);
            }
        }

        // Create the user with basic fields
        $userData = [
            'name' => $request->name,
            'phone' => $request->phone,
            'role' => $request->role,
            'is_approved' => $request->role === 'driver' ? false : true,
            'is_admin' => $request->role === 'admin',
            'password' => Hash::make($request->password),
        ];
        
        // Add county_id and subcounty for drivers
        if ($request->role === 'driver') {
            $userData['county_id'] = $request->county_id;
            $userData['subcounty'] = $request->subcounty;
        }

        $user = User::create($userData);

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