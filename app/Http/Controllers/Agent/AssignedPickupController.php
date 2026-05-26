<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Donation;

class AssignedPickupController extends Controller
{
    public function index()
    {
        return view('agent.pickups.index', [
            'donations' => Donation::with(['donorArea', 'targetArea'])
                ->where('pickup_agent_id', auth()->id())
                ->latest('pickup_assigned_at')
                ->paginate(20),
        ]);
    }

    public function show(Donation $donation)
    {
        abort_unless((int) $donation->pickup_agent_id === (int) auth()->id(), 403);

        return view('agent.pickups.show', ['donation' => $donation->load(['donorArea', 'targetArea'])]);
    }
}
