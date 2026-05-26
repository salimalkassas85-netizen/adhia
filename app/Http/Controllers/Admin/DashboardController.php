<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Support\AdminAreaScope;

class DashboardController extends Controller
{
    public function __invoke(AdminAreaScope $scope)
    {
        $areaId = $scope->areaId();
        $requests = $scope->requests();
        $donations = $scope->donations();
        $areas = Area::query()
            ->when($areaId, fn ($query) => $query->where('id', $areaId))
            ->withCount('beneficiaryRequests')
            ->withSum(['donations as meat_kg_sum' => fn ($query) => $query->where('donation_type', 'meat_kg')], 'meat_kg')
            ->orderBy('name')
            ->get();

        return view('admin.dashboard', [
            'currentArea' => auth()->user()->area,
            'isGlobalAdmin' => $scope->isGlobal(),
            'totalRequests' => (clone $requests)->count(),
            'pendingRequests' => (clone $requests)->where('status', 'pending')->count(),
            'approvedRequests' => (clone $requests)->where('status', 'approved')->count(),
            'assignedRequests' => (clone $requests)->whereNotNull('assigned_agent_id')->count(),
            'deliveredRequests' => (clone $requests)->where('status', 'delivered')->count(),
            'failedRequests' => 0,
            'totalDonations' => (clone $donations)->count(),
            'pendingDonations' => (clone $donations)->where('status', 'pending')->count(),
            'confirmedDonations' => (clone $donations)->whereIn('status', ['received', 'completed'])->count(),
            'notifications' => auth()->user()->adminNotifications()->latest()->limit(8)->get(),
            'recentRequests' => (clone $requests)->with(['area', 'assignedAgent'])->latest()->limit(8)->get(),
            'recentDonations' => (clone $donations)->with(['donorArea', 'targetArea'])->latest()->limit(8)->get(),
            'donationsByArea' => (clone $donations)->selectRaw('target_area_id, count(*) as total')->groupBy('target_area_id')->with('targetArea')->get(),
            'requestsByArea' => (clone $requests)->selectRaw('area_id, count(*) as total')->groupBy('area_id')->with('area')->get(),
            'areas' => $areas,
        ]);
    }
}
