<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PledgeController extends Controller
{
    public function show()
    {
        return view('pledge');
    }

    public function accept(Request $request)
    {
        $request->user()->forceFill(['pledge_accepted_at' => now()])->save();

        return redirect($request->user()->isAdmin() ? route('admin.dashboard') : route('home'));
    }
}
