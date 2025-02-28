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
    /**
     * Customer Registration API
     */
    public function registerCustomer(Request $request)
    {
            

        $validated = Validator::make($request->all(),[
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:male,female,other'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'regex:/^\+\d{12}$/', 'unique:users,phone'],
            'county_id' => 'required|exists:counties,id',
            'subcounty' => 'required|string|max:255',
            'password' => ['required', 'string', 'digits:4']]);
        
            if ($validated->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validated->errors()
                ], 400);
            }
            
            $validatedData = $validated->validated();
        // Create user
        $user = User::create([
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'gender' => $validatedData['gender'],
            'email' => $validatedData['email'],
            'phone' => $validatedData['phone'],
            'role' => 'customer',
            'county_id' => $validatedData['county_id'],
            'subcounty' => $validatedData['subcounty'],
            'password' => Hash::make($validatedData['password']),
        ]);


        return response()->json([
            'status' => 'success',
            'message' => 'Customer registration successful. Please log in.',
            'user' => $user
        ], 201);
    }

    /**
     * Driver Registration API
     */
    public function registerDriver(Request $request)
    {
        // Validation rules
        $validated = Validator::make($request->all(),[
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:male,female,other'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'regex:/^\+\d{12}$/', 'unique:users,phone'],
            'county_id' => 'required|exists:counties,id',
            'subcounty' => 'required|string|max:255',
            'password' => ['required', 'string', 'digits:4']]);
        
            if ($validated->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validated->errors()
                ], 400);
            }
            
            $validatedData = $validated->validated();
        // Create user
        $user = User::create([
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'gender' => $validatedData['gender'],
            'email' => $validatedData['email'],
            'phone' => $validatedData['phone'],
            'role' => 'driver',
            'county_id' => $validatedData['county_id'],
            'subcounty' => $validatedData['subcounty'],
            'password' => Hash::make($validatedData['password']),
        ]);


        return response()->json([
            'status' => 'success',
            'message' => 'Driver registration successful. Please log in.',
            'user' => $user
        ], 201);
    }
}

