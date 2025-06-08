<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Roles;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;

/**
 * This controller manages roles in the admin area, like creating or editing user roles.
 */
class RolesController extends Controller
{
    /**
     * Shows a page with a list of all roles.
     *
     * Gets all roles from the database and loads a page to show them.
     */
    public function index()
    {
        $roles = Roles::all();
        return view('admin.roles', compact('roles'));
    }

    /**
     * Shows a form to create a new role.
     *
     * Not used right now.
     */
    public function create()
    {
        //
    }

    /**
     * Saves a new role to the database.
     *
     * Takes the role name and status, checks if they're correct, and saves the role
     * with details like who created it and when.
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
     * Shows details of a specific role.
     *
     * Not used right now.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Gets a role to edit it.
     *
     * Finds a role by its ID and sends it back to show in an edit form.
     */
    public function edit(string $id)
    {
        $role = Roles::findOrFail($id);
        return $this->jsonSuccess($role, 'Role retrieved successfully.');
    }

    /**
     * Updates a role in the database.
     *
     * Finds the role by ID, checks the new details, and updates it with
     * the new name, status, and who updated it.
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
     * Deletes a role from the database.
     *
     * Finds a role by its ID and removes it.
     */
    public function destroy(string $id)
    {
        $role = Roles::findOrFail($id);
        $role->delete();
        return $this->jsonSuccess($role, 'Role deleted successfully.');
    }

    /**
     * Gets a list of all active roles.
     *
     * Grabs only the active roles and sends them back as a list
     * for things like dropdowns or role selection.
     */
    public function getRoles()
    {
        $roles = Roles::where('status', 1)->get();
        return $this->jsonSuccess($roles, 'Roles retrieved successfully.');
    }
}