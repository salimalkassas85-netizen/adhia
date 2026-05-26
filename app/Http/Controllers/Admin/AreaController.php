<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAreaRequest;
use App\Models\Area;

class AreaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.areas.index', ['areas' => Area::orderBy('name')->paginate(20)]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.areas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAreaRequest $request)
    {
        Area::create($request->safe()->merge(['active' => $request->boolean('active')])->all());

        return redirect()->route('admin.areas.index')->with('status', 'تم حفظ المنطقة.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Area $area)
    {
        return redirect()->route('admin.areas.edit', $area);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Area $area)
    {
        return view('admin.areas.edit', compact('area'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreAreaRequest $request, Area $area)
    {
        $area->update($request->safe()->merge(['active' => $request->boolean('active')])->all());

        return redirect()->route('admin.areas.index')->with('status', 'تم تحديث المنطقة.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Area $area)
    {
        $area->delete();

        return redirect()->route('admin.areas.index')->with('status', 'تم حذف المنطقة.');
    }
}
