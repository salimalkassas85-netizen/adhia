<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\BeneficiaryRequest;
use App\Models\Donation;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $query = BeneficiaryRequest::where('assigned_agent_id', auth()->id());

        return view('agent.dashboard', [
            'assignedCount' => (clone $query)->count(),
            'onTheWayCount' => (clone $query)->where('status', 'on_the_way')->count(),
            'deliveredCount' => (clone $query)->where('status', 'delivered')->count(),
            'pickupCount' => Donation::where('pickup_agent_id', auth()->id())->count(),
        ]);
    }
}
