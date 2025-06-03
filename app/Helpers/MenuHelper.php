<?php
use App\Models\Menu as MasterMenu;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
if (!function_exists('hasPermission')) {
    function hasPermission($permissionKey) {
        $user = Auth::user();
        if (!$user) {
            return false;
        }
        $permissions = DB::table('tbl_role_permission_access')->join('master_permissions', 'tbl_role_permission_access.permission_id', '=', 'master_permissions.id')->where('tbl_role_permission_access.role_id', $user->role_id)->pluck('master_permissions.permission_key')->toArray();
        return in_array($permissionKey, $permissions);
    }
}
if (!function_exists('getMenuTree')) {
    function getUserRoleName($id) {
        return User::select('master_roles.role_name')->leftJoin('master_roles', 'users.role_id', '=', 'master_roles.id')->where('users.id', $id)->first();
    }
    function getMenuTree($roleId) {
        if ($roleId == 1) {
            return MasterMenu::where('status', 1)->orderBy('order')->get();
        }
        return MasterMenu::select('menus.*')->leftJoin('tbl_role_menu_access', 'menus.id', '=', 'tbl_role_menu_access.menu_id')->where('menus.status', 1)->where('tbl_role_menu_access.role_id', $roleId)->orderBy('menus.order')->get();
    }
    function buildMenuTree($menuItems, $parentId = null) {
        $branch = [];
        foreach ($menuItems as $menu) {
            if ($menu->parent_id == $parentId) {
                $menu->children = buildMenuTree($menuItems, $menu->id);
                $branch[] = $menu;
            }
        }
        return $branch;
    }
    function hasChildren($menuItems, $parentId) {
        return $menuItems->contains('parent_id', $parentId);
    }
    function renderMenuTree($menuTree, $menuItems, $level = 1) {
        if (empty($menuTree)) {
            return;
        }
        echo '<ul class="nav' . ($level > 1 ? ' nav-sm' : '') . '">';
        foreach ($menuTree as $menu) {
            if (empty($menu->permission_name)) {
                continue;
            }
            if (!auth()->user()->can($menu->permission_name)) {
                continue;
            }
            $hasChildren = hasChildren($menuItems, $menu->id);
            $collapseId = 'sidebar' . $menu->id;
            echo '<li class="nav-item">';
            $iconClass = !empty($menu->icon) ? htmlspecialchars($menu->icon) : (($menu->parent_id === null) ? 'ri-folder-line' : '');
            if ($hasChildren) {
                echo '<a class="nav-link menu-link" href="#' . $collapseId . '" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="' . $collapseId . '">';
                if ($iconClass) {
                    echo '<i class="' . $iconClass . '"></i> ';
                }
                echo '<span data-key="' . htmlspecialchars($menu->data_key) . '">' . htmlspecialchars($menu->title) . '</span>';
                echo '</a>';
                echo '<div class="collapse menu-dropdown" id="' . $collapseId . '">';
                renderMenuTree($menu->children, $menuItems, $level + 1);
                echo '</div>';
            } else {
                echo '<a href="' . htmlspecialchars($menu->url) . '" class="nav-link menu-link">';
                if ($iconClass) {
                    echo '<i class="' . $iconClass . '"></i> ';
                }
                echo '<span data-key="' . htmlspecialchars($menu->data_key) . '">' . htmlspecialchars($menu->title) . '</span>';
                echo '</a>';
            }
            echo '</li>';
        }
        echo '</ul>';
    }
}
