<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdminUserRequest;
use App\Http\Requests\UpdateAdminUserRequest;
use App\Models\Area;
use App\Models\User;

class AdminUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.admin-users.index', [
            'admins' => User::where('role', 'admin')->with('area')->latest()->paginate(20),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.admin-users.create', [
            'areas' => Area::where('active', true)->orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAdminUserRequest $request)
    {
        User::create($request->safe()->merge(['role' => 'admin'])->all());

        return redirect()->route('admin.admin-users.index')->with('status', 'تم إنشاء حساب أدمن المنطقة.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $adminUser)
    {
        return redirect()->route('admin.admin-users.edit', $adminUser);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $adminUser)
    {
        abort_unless($adminUser->isAdmin(), 404);

        return view('admin.admin-users.edit', [
            'adminUser' => $adminUser,
            'areas' => Area::where('active', true)->orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAdminUserRequest $request, User $adminUser)
    {
        abort_unless($adminUser->isAdmin(), 404);

        $data = $request->validated();
        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $adminUser->update($data);

        return redirect()->route('admin.admin-users.index')->with('status', 'تم تحديث حساب الأدمن.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $adminUser)
    {
        abort_unless($adminUser->isAdmin(), 404);
        abort_if($adminUser->is(auth()->user()), 422, 'لا يمكن حذف حسابك الحالي.');

        $adminUser->delete();

        return redirect()->route('admin.admin-users.index')->with('status', 'تم حذف حساب الأدمن.');
    }
}
