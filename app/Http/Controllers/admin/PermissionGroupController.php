<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PermissionGroup;
use App\Http\Requests\PermissionGroupRequest;
use Illuminate\Support\Facades\Auth;

class PermissionGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $permissionGroups = PermissionGroup::all()->where('status', 1);
        return $this->jsonSuccess($permissionGroups, 'Permission groups fetched successfully.');
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(PermissionGroupRequest $request)
    {
        $group = PermissionGroup::create([
            'name' => $request->group_name,
            'status' => 1,
            'created_by' => Auth::id(),
        ]);

        return $this->jsonSuccess($group, 'Permission group created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
