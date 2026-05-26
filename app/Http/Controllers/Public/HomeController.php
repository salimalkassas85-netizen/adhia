<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\BeneficiaryRequest;
use App\Models\Donation;

class HomeController extends Controller
{
    public function __invoke()
    {
        return view('public.home', [
            'areasCount' => Area::where('active', true)->count(),
            'deliveredCount' => BeneficiaryRequest::where('status', 'delivered')->count(),
            'donationsCount' => Donation::whereIn('status', ['received', 'completed'])->count(),
        ]);
    }
}
