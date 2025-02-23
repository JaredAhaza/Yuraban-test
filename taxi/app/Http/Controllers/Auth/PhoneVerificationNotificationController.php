<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PhoneVerificationNotificationController extends Controller
{
    /**
     * Send a new phone verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        // Check if the user has verified their phone
        if ($request->user()->hasVerifiedPhone()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        // Send the phone verification notification
        $request->user()->sendPhoneVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}
