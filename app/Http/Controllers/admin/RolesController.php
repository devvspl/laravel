<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Roles;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Permission;
use App\Models\PermissionGroup;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;


class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Roles::all();
        return view('admin.roles', compact('roles'));
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
    public function store(StoreRoleRequest $request)
    {
        $roles = Roles::create([
            'name' => $request->role_name,
            'guard_name' => 'web',
            'status' => $request->is_active,
            'created_by' => Auth::id(),
            'created_at' => now(),
        ]);
        return $this->jsonSuccess($roles, 'Role created successfully.');
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
        $role = Roles::findOrFail($id);
        return $this->jsonSuccess($role, 'Role retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, string $id)
    {
        $role = Roles::findOrFail($id);
        $role->update([
            'name' => $request->role_name,
            'guard_name' => 'web',
            'status' => $request->is_active,
            'updated_by' => Auth::id(),
            'updated_at' => now(),
        ]);
        return $this->jsonSuccess($role, 'Role updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Roles::findOrFail($id);
        $role->delete();
        return $this->jsonSuccess($role, 'Role deleted successfully.');
    }

    public function getRoles()
    {
        $roles = Roles::where('status', 1)->get();
        return $this->jsonSuccess($roles, 'Roles retrieved successfully.');
    }

    /**
     * Show the form for assigning permissions to the role.
     */

}
