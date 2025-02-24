<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $filterDate = $request->input('filter_date');

        $customers = User::where('role', 'customer');

        if ($filterDate) {
            $customers->whereDate('created_at', $filterDate);
        }

        $customers = $customers->orderBy('created_at', 'desc')->paginate(10);

        return view('customers.index', compact('customers', 'filterDate'));
    }
}
