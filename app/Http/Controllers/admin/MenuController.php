<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;
use App\Http\Requests\StoreMenuRequest;
use App\Http\Requests\UpdateMenuRequest;
use Illuminate\Support\Str;

/**
 * This controller handles everything related to menus in the admin area.
 */
class MenuController extends Controller
{
    /**
     * Shows a page with a list of all menus.
     *
     * Gets all menus from the database and loads a page to display them.
     */
    public function index()
    {
        $menus = Menu::all();
        return view('admin.menu', compact('menus'));
    }

    /**
     * Shows a form to create a new menu.
     *
     * Not used right now.
     */
    public function create()
    {
        //
    }

    /**
     * Saves a new menu to the database.
     *
     * Checks if the input is correct, then creates a new menu with details
     * like name, icon, and link. It also makes a simple version of the menu name
     * (like "Main Menu" becomes "main-menu").
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
     * Shows details of a specific menu.
     *
     * Not used right now.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Gets a menu to edit it.
     *
     * Finds a menu by its ID and sends it back to show in an edit form.
     */
    public function edit(string $id)
    {
        $menu = Menu::findOrFail($id);
        return $this->jsonSuccess($menu, 'Menu fetched successfully.');
    }

    /**
     * Updates a menu in the database.
     *
     * Checks if the new details are correct, then updates the menu
     * with new information like name, icon, or link.
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
     * Deletes a menu from the database.
     *
     * Finds a menu by its ID and removes it.
     */
    public function destroy(string $id)
    {
        $menu = Menu::findOrFail($id);
        $menu->delete();
        return $this->jsonSuccess($menu, 'Menu deleted successfully.');
    }

    /**
     * Gets a list of all active menus.
     *
     * Grabs only the active menus and sends them back as a list
     * for things like showing a navigation menu on a webpage.
     */
    public function menuList()
    {
        $query = Menu::select('id', 'title', 'url', 'icon', 'parent_id', 'order', 'data_key', 'status', 'created_at', 'updated_at')
            ->where('status', 1);
        $data = $query->get();
        return response()->json($data);
    }
}