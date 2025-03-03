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

    public function getCounties()
    {
        $counties = County::all();

        return response()->json([
            'status' => 'success',
            'data' => $counties
        ], 200);
    }

    public function getSubCounties($county_id)
    {
        $county = County::find($county_id);
    
        if (!$county) {
            return response()->json([
                'status' => 'error',
                'message' => 'County not found'
            ], 404);
        }
    
        return response()->json([
            'status' => 'success',
            'data' => $county->sub_counties // This returns the array
        ], 200);
    }
    


    public function store(Request $request) 
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
            'county' => 'required|exists:counties,county_name',
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
            'county' => $validatedData['county_name'],
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

