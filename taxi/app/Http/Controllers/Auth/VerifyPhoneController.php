<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class VerifyPhoneController extends Controller
{
    /**
     * Verify the user's phone number.
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'verification_code' => 'required|string', // Assuming you send a verification code
        ]);

        // Logic to verify the phone number
        if ($request->user()->phone !== $request->phone || !$this->verifyCode($request->verification_code)) {
            throw ValidationException::withMessages([
                'verification_code' => __('The provided verification code is invalid.'),
            ]);
        }

        // Mark the phone as verified
        $request->user()->markPhoneAsVerified();

        // Redirect to intended route with a success message
        return redirect()->intended(route('dashboard'))->with('status', 'Phone verified successfully!');
    }

    protected function verifyCode($code)
    {
        // Implement your logic to verify the code
        // For example, you might check the code against a database or an external service
        // This is a placeholder for actual verification logic
        // Example: return $code === session('verification_code');

        return true; // Placeholder for actual verification logic
    }
}
