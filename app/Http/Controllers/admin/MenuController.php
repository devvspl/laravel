<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;
use App\Http\Requests\StoreMenuRequest;
use App\Http\Requests\UpdateMenuRequest;
 use Illuminate\Support\Str;
class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $menus = Menu::all();
        return view('admin.menu', compact('menus'));
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
   

    public function store(StoreMenuRequest $request)
    {
        $validated = $request->validated();

        $menu = Menu::create([
            'title' => $validated['menu_name'],
            'data_key' => Str::kebab($validated['menu_name']), 
            'parent_id' => $validated['parent_id'],
            'icon' => $validated['icon'],
            'order' => $validated['order'],
            'url' => $validated['url'],
            'permission_name' => $validated['permission'],
            'status' => $validated['is_active'],
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return $this->jsonSuccess($menu, 'Menu created successfully.');
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
        $menu = Menu::findOrFail($id);
        return $this->jsonSuccess($menu, 'menu fetched successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMenuRequest $request, Menu $menu)
    {
        $validated = $request->validated();
        $menu->update([
            'title' => $validated['menu_name'],
            'parent_id' => $validated['parent_id'],
            'icon' => $validated['icon'],
            'order' => $validated['order'],
            'url' => $validated['url'],
            'permission_name' => $validated['permission'],
            'status' => $validated['is_active'],
            'updated_by' => auth()->id(),
        ]);
        return $this->jsonSuccess($menu, 'Menu updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $menu = Menu::findOrFail($id);
        $menu->delete();
        return $this->jsonSuccess($menu, 'Menu deleted successfully.');
    }

    public function menuList()
    {
        $query = Menu::select('id', 'title', 'url', 'icon', 'parent_id', 'order', 'data_key', 'status', 'created_at', 'updated_at')->where('status', 1);
        $data = $query->get();
        return response()->json($data);
    }
}
