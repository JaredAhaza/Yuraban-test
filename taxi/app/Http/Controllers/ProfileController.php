<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validatedData = $request->validated();
    
        // Update user details
        $user->update([
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'email' => $validatedData['email'],
            'phone' => $validatedData['phone'] ?? null,
        ]);
    
        return redirect()->route('dashboard')->with('status', 'profile-updated');
    }
    
        /**
     * Get the authenticated user's profile (API)
     */
    public function getProfileApi()
    {
        return response()->json([
            'status' => 'success',
            'user' => Auth::user()
        ], 200);
    }

    /**
     * Update the authenticated user's profile (API)
     */
    public function updateProfileApi(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'gender' => 'nullable|in:male,female,other',
            'email' => 'nullable|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|regex:/^\+\d{12}$/|unique:users,phone,' . $user->id,
            'county_id' => 'nullable|exists:counties,id',
            'subcounty' => 'nullable|string|max:255'
        ];

        $validatedData = $request->validate($rules);

        $user->update($validatedData);

        return response()->json([
            'status' => 'success',
            'message' => 'Profile updated successfully.',
            'user' => $user
        ], 200);
    }

    /**
     * Change password (API)
     */
    public function changePasswordApi(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:4|different:current_password',
            'confirm_password' => 'required|same:new_password'
        ];

        $validatedData = $request->validate($rules);

        if (!Hash::check($validatedData['current_password'], $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Current password is incorrect.'
            ], 400);
        }

        $user->update([
            'password' => Hash::make($validatedData['new_password'])
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Password changed successfully.'
        ], 200);
    
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
