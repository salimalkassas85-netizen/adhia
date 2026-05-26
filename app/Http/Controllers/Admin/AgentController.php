<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAgentRequest;
use App\Http\Requests\UpdateAgentRequest;
use App\Models\Area;
use App\Models\User;
use App\Support\AdminAreaScope;

class AgentController extends Controller
{
    public function index(AdminAreaScope $scope)
    {
        return view('admin.agents.index', [
            'agents' => $scope->agents()->with('area')->latest()->paginate(20),
        ]);
    }

    public function create(AdminAreaScope $scope)
    {
        return view('admin.agents.create', [
            'areas' => Area::where('active', true)
                ->when($scope->areaId(), fn ($query, $areaId) => $query->where('id', $areaId))
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(StoreAgentRequest $request, AdminAreaScope $scope)
    {
        $data = $request->validated();
        if ($scope->areaId()) {
            $data['area_id'] = $scope->areaId();
        }

        User::create(array_merge($data, ['role' => 'agent']));

        return redirect()->route('admin.agents.index')->with('status', 'تم إنشاء حساب فريق التوزيع.');
    }

    public function show(User $agent)
    {
        return redirect()->route('admin.agents.edit', $agent);
    }

    public function edit(User $agent, AdminAreaScope $scope)
    {
        abort_unless($agent->isAgent(), 404);
        abort_unless($scope->areaId() === null || (int) $agent->area_id === (int) $scope->areaId(), 403);

        return view('admin.agents.edit', [
            'agent' => $agent,
            'areas' => Area::where('active', true)
                ->when($scope->areaId(), fn ($query, $areaId) => $query->where('id', $areaId))
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function update(UpdateAgentRequest $request, User $agent, AdminAreaScope $scope)
    {
        abort_unless($agent->isAgent(), 404);
        abort_unless($scope->areaId() === null || (int) $agent->area_id === (int) $scope->areaId(), 403);

        $data = $request->validated();
        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }
        if ($scope->areaId()) {
            $data['area_id'] = $scope->areaId();
        }

        $agent->update($data);

        return redirect()->route('admin.agents.index')->with('status', 'تم تحديث حساب فريق التوزيع.');
    }

    public function destroy(User $agent, AdminAreaScope $scope)
    {
        abort_unless($agent->isAgent(), 404);
        abort_unless($scope->areaId() === null || (int) $agent->area_id === (int) $scope->areaId(), 403);

        $agent->delete();

        return redirect()->route('admin.agents.index')->with('status', 'تم حذف الحساب.');
    }
}
