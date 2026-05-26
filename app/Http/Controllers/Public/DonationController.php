<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDonationRequest;
use App\Models\Area;
use App\Models\Donation;
use App\Services\DonationService;

class DonationController extends Controller
{
    public function create()
    {
        return view('public.donate', [
            'areas' => Area::where('active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(StoreDonationRequest $request, DonationService $service)
    {
        $donation = $service->create($request->validated());

        return redirect()->route('public.donation.success', $donation->code);
    }

    public function success(string $code)
    {
        $donation = Donation::where('code', $code)->firstOrFail();

        return view('public.donation-success', ['code' => $donation->code, 'status' => $donation->status]);
    }
}
