<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    // === РОЛИ ===
    public function rolesIndex()
    {
        $roles = Role::withCount('users')->get();
        return view('admin.roles.index', compact('roles'));
    }

    public function storeRole(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles',
            'description' => 'nullable|string',
            'is_system_role' => 'boolean',
        ]);

        Role::create($validated);
        
        return redirect()->route('settings.index', ['tab' => 'users-roles'])
            ->with('success', 'Роль успешно создана');
    }

    public function show($id)
    {
        $role = Role::findOrFail($id);
        return response()->json($role);
    }

    public function updateRole(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_system_role' => 'boolean' // Добавьте эту валидацию
        ]);

        // Преобразуем чекбокс в boolean
        $validated['is_system_role'] = $request->has('is_system_role');

        $role->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Роль успешно обновлена'
        ]);
    }

    public function destroyRole($id)
    {
        $role = Role::findOrFail($id);
        
        if ($role->is_system_role) {
            return response()->json([
                'success' => false,
                'message' => 'Нельзя удалить системную роль'
            ], 422);
        }

        $role->delete();

        return response()->json([
            'success' => true,
            'message' => 'Роль успешно удалена'
        ]);
    }

    // === ПРАВА ===
    public function permissionsIndex()
    {
        $permissions = Permission::with('roles')->get();
        $roles = Role::all();
        
        return view('admin.permissions.index', compact('permissions', 'roles'));
    }

    public function syncRolePermissions(Request $request, Role $role)
    {
        $role->permissions()->sync($request->permission_ids);
        return redirect()->back()->with('success', 'Права обновлены!');
    }
}