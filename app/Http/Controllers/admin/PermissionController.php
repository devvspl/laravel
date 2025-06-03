<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Permission;
use App\Http\Requests\StorePermissionRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Roles;
use App\Models\User;
use App\Models\PermissionGroup;
use Illuminate\Support\Facades\Cache;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        $query = Permission::join('permission_groups', 'permissions.permission_group_id', '=', 'permission_groups.id')
            ->select('permissions.*', 'permission_groups.name as group_name');

        if ($request->filled('group_id')) {
            $query->where('permissions.permission_group_id', $request->group_id);
        }

        $permissions = $query->get();
        $roles = Roles::where('status', 1)->get();
        $group = PermissionGroup::all();

        return view('admin.permissions', compact('permissions', 'roles', 'group'));
    }


    public function create()
    {
        // If needed, you can return a view for creating permissions
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

        Cache::forget('spatie.permission.cache');

        return $this->jsonSuccess($permission, 'Permission created successfully.');
    }

    public function show(string $id)
    {
        // If needed, you can implement this method to display a specific permission
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

        Cache::forget('spatie.permission.cache');

        return $this->jsonSuccess($permission, 'Permission updated successfully.');
    }

    public function destroy(string $id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();

        Cache::forget('spatie.permission.cache');

        return $this->jsonSuccess(null, 'Permission deleted successfully.');
    }

    public function assignPermissions(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permission_id' => 'required|exists:permissions,id',
            'isChecked' => 'required|boolean',
        ]);

        try {
            $role = Roles::findOrFail($request->role_id);
            $permission = Permission::findOrFail($request->permission_id);

            if ($role->guard_name !== $permission->guard_name) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Guard mismatch between role and permission.',
                    ],
                    400
                );
            }

            if ($request->isChecked) {
                $role->givePermissionTo($permission->name);
            } else {
                $role->revokePermissionTo($permission->name);
            }

            Cache::forget('spatie.permission.cache');

            return response()->json([
                'success' => true,
                'message' => 'Permission updated successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Failed to update permission: ' . $e->getMessage(),
                ],
                500
            );
        }
    }

    public function getPermissions(string $id)
    {
        $user = User::findOrFail($id);
        $permissions = $user->getDirectPermissions()
            ->load('group')
            ->map(function ($permission) {
                return [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'group' => $permission->group ? $permission->group->name : 'Other',
                ];
            })
            ->groupBy('group')
            ->toArray();

        return $this->jsonSuccess($permissions, 'Permissions fetched successfully.');
    }

    public function getAllPermissions()
    {
        $permissions = Permission::with('group')
            ->get()
            ->map(function ($permission) {
                return [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'group' => $permission->group ? $permission->group->name : 'Other',
                ];
            })
            ->groupBy('group')
            ->toArray();

        return $this->jsonSuccess($permissions, 'All permissions fetched successfully.');
    }

    public function assignPermission(Request $request, string $id)
    {
        $request->validate([
            'permission_id' => 'required|exists:permissions,id',
        ]);

        $user = User::findOrFail($id);
        $permission = Permission::findOrFail($request->permission_id);

        $user->givePermissionTo($permission->name);

        Cache::forget('spatie.permission.cache');

        return $this->jsonSuccess(null, 'Permission assigned successfully.');
    }

    public function revokePermission(Request $request, string $id)
    {
        $request->validate([
            'permission_id' => 'required|exists:permissions,id',
        ]);

        $user = User::findOrFail($id);
        $permission = Permission::findOrFail($request->permission_id);

        $user->revokePermissionTo($permission->name);

        Cache::forget('spatie.permission.cache');

        return $this->jsonSuccess(null, 'Permission revoked successfully.');
    }
}