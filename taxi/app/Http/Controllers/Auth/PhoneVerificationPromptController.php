<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PhoneVerificationPromptController extends Controller
{
    /**
     * Show the phone verification prompt.
     */
    public function __invoke(Request $request): View
    {
        return view('auth.verify-phone'); // Create this view to prompt for phone verification
    }
}
