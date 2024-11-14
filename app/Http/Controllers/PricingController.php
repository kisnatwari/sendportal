<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PricingController extends Controller
{
    //
    public function index()
    {
        $user = Auth::user();
        if ($user->subscription_status === 'active')
            return redirect("/");
        return view('pricing.index');
    }

    public function subscribe(Request $request)
    {
        $user = Auth::user();
        // Update the subscription status
        $user->subscription_status = 'active';
        $user->save();

        return redirect('/');
    }
}
