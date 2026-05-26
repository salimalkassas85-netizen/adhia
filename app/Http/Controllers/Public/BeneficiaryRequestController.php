<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBeneficiaryRequest;
use App\Models\Area;
use App\Models\BeneficiaryRequest;
use App\Services\BeneficiaryRequestService;

class BeneficiaryRequestController extends Controller
{
    public function create()
    {
        return view('public.request-gift', [
            'areas' => Area::where('active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(StoreBeneficiaryRequest $request, BeneficiaryRequestService $service)
    {
        $giftRequest = $service->create($request->validated());

        return redirect()->route('public.request.success', $giftRequest->code);
    }

    public function success(string $code)
    {
        $giftRequest = BeneficiaryRequest::where('code', $code)->firstOrFail();

        return view('public.request-success', ['code' => $giftRequest->code]);
    }
}
