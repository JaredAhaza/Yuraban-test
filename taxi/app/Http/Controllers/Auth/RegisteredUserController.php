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
    public function store(Request $request): RedirectResponse
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
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }
        } else {
            // For non-drivers, just validate the base rules
            $validator = Validator::make($request->all(), $rules);
            
            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        // Check if the phone number is already registered
        $existingUser = User::where('phone', $request->phone)->first();
        if ($existingUser) {
            // Check if the user is declined
            if ($existingUser->is_declined) {
                return redirect()->back()->withErrors([
                    'phone' => 'Your application has been denied. Please try again after 3 months.',
                ])->withInput();
            }

            // Check if the user is approved
            if ($existingUser->is_approved) {
                return redirect()->back()->withErrors([
                    'phone' => 'You are already registered and approved.',
                ])->withInput();
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
            return redirect()->route('waiting.approval')->with('status', 'Your application is under review.');
        } elseif ($user->role !== 'driver') {
            // Redirect to login page for non-driver roles
            return redirect()->route('login')->with('status', 'Registration successful. Please log in.');
        }

        // Redirect to login page
        return redirect()->route('login')->with('status', 'Your account has been created. Please login.');
    }
}