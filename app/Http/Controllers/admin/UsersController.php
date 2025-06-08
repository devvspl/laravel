<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Models\Roles;
use Illuminate\Support\Facades\Hash;

/**
 * This controller handles user management in the admin area, like creating, updating, or deleting users.
 */
class UsersController extends Controller
{
    /**
     * Shows a page with a list of all users and their roles.
     *
     * Gets all users and active roles from the database and loads a page to show them.
     */
    public function index()
    {
        $users = User::all();
        $roles = Roles::where('status', 1)->get();
        return view('admin.users', compact('users', 'roles'));
    }

    /**
     * Shows a form to create a new user.
     *
     * Not used right now.
     */
    public function create()
    {
        //
    }

    /**
     * Saves a new user to the database.
     *
     * Checks the input, creates a user with name, email, and a secure password,
     * sets their status, and assigns roles if provided.
     */
    public function store(StoreUserRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => $request->is_active,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        // Assign roles to the user if any are provided
        if ($request->has('role_id') && !empty($request->role_id)) {
            $roleIds = is_array($request->role_id) ? $request->role_id : [$request->role_id];
            $roleIds = array_map('intval', $roleIds);
            $user->assignRole($roleIds);
        }

        return $this->jsonSuccess($user, 'User created successfully.');
    }

    /**
     * Shows details of a specific user.
     *
     * Not used right now.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Gets a user to edit their details.
     *
     * Finds a user by their ID, gets their current roles, and sends the data back
     * for an edit form.
     */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        $hasRoles = $user->roles()->pluck('id');
        $userData = $user->toArray();
        $userData['role_ids'] = $hasRoles;
        return $this->jsonSuccess($userData, 'User fetched successfully.');
    }

    /**
     * Updates a user in the database.
     *
     * Finds a user by ID, updates their name and status, and syncs their roles
     * (adds or removes roles based on the input).
     */
    public function update(UpdateUserRequest $request, string $id)
    {
        $user = User::findOrFail($id);
        $user->update([
            'name' => $request->name,
            'status' => $request->is_active,
            'updated_by' => auth()->id(),
            'updated_at' => now(),
        ]);

        // Update user roles: assign new ones or remove all if none provided
        if ($request->has('role_id') && !empty($request->role_id)) {
            $roleIds = is_array($request->role_id) ? $request->role_id : [$request->role_id];
            $roleIds = array_map('intval', $roleIds);
            $user->syncRoles($roleIds);
        } else {
            $user->syncRoles([]);
        }

        return $this->jsonSuccess($user, 'User updated successfully.');
    }

    /**
     * Deletes a user from the database.
     *
     * Finds a user by their ID and removes them.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return $this->jsonSuccess(null, 'User deleted successfully.');
    }

    /**
     * Shows the profile page for the logged-in user.
     *
     * Gets the details of the current user and loads their profile page.
     */
    public function profile()
    {
        $id = Auth::id();
        $user_detail = User::find($id);
        return view('admin.profile', compact('user_detail'));
    }
}