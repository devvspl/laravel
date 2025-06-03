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

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        $roles = Roles::where('status', 1)->get();
        return view('admin.users', compact('users', 'roles'));

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

        if ($request->has('role_id') && !empty($request->role_id)) {
            $roleIds = is_array($request->role_id) ? $request->role_id : [$request->role_id];
            $roleIds = array_map('intval', $roleIds);
            $user->assignRole($roleIds);
        }

        return $this->jsonSuccess($user, 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

    }

    /**
     * Show the form for editing the specified resource.
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
     * Update the specified resource in storage.
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
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return $this->jsonSuccess(null, 'User deleted successfully.');
    }

    public function profile()
    {
        $id = Auth::id();
        $user_detail = User::find($id);
        return view('admin.profile', compact('user_detail'));
    }
}
