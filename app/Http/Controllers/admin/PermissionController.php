<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Permission;
use App\Http\Requests\StorePermissionRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\PermissionGroup;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::join('permission_groups', 'permissions.permission_group_id', '=', 'permission_groups.id')
            ->select('permissions.*', 'permission_groups.name as group_name')
            ->get();

        return view('admin.permissions.index', compact('permissions'));
    }

    public function create()
    {
        
    }

    public function store(StorePermissionRequest $request)
    {
        $permissionKey = Str::slug($request->permission_name, '_');
        $permission = Permission::create([
            'name' => $request->permission_name,
            'permission_key' => $permissionKey,
            'permission_group_id' => $request->group_id,
            'status' => $request->is_active,
            'guard_name' => 'web',
            'created_by' => Auth::id(),
        ]);
        return $this->jsonSuccess($permission, 'Permission created successfully.');
    }

    public function show(string $id)
    {
        
    }

    public function edit(string $id)
    {
        $permission = Permission::findOrFail($id);
        return $this->jsonSuccess($permission, 'Permission fetched successfully.');
    }

    public function update(StorePermissionRequest $request, string $id)
    {

        $permission = Permission::findOrFail($id);
        $permission->update([
            'name' => $request->permission_name,
            'permission_key' => Str::slug($request->permission_name, '_'),
            'guard_name' => 'web',
            'updated_by' => Auth::id(),
            'updated_at' => now(),
            'permission_group_id' => $request->group_id,
            'status' => $request->is_active,
        ]);
        return $this->jsonSuccess($permission, 'Permission updated successfully.');
    }

    public function destroy(string $id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();
        return $this->jsonSuccess(null, 'Permission deleted successfully.');
    }
}
