<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    // === ПОЛЬЗОВАТЕЛИ ===
    public function index()
    {
        $users = User::with(['roles', 'department', 'position'])->get();
        $roles = Role::all();
        $departments = Department::all();
        
        return view('users.index', compact('users', 'roles', 'departments'));
    }

    public function store(Request $request)
    {
        // Создание пользователя (из EmployeeOnboardingController)
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'position_id' => 'required|exists:positions,id',
            'department_id' => 'required|exists:departments,id',
            'hire_date' => 'required|date',
            'roles' => 'nullable|array',
        ]);

        $user = User::create($validated);
        
        if (!empty($validated['roles'])) {
            $user->roles()->sync($validated['roles']);
        }

        return redirect()->route('admin.users.index')->with('success', 'Сотрудник создан!');
    }

    public function toggleStatus(User $user)
    {
        $user->is_active = !$user->is_active;
        $user->save();
        
        return response()->json(['success' => true, 'is_active' => $user->is_active]);
    }

    public function destroy(User $user)
    {
        if ($user->is_admin) {
            return response()->json(['success' => false, 'message' => 'Нельзя удалить администратора'], 403);
        }

        $user->delete();
        return response()->json(['success' => true]);
    }

    // === МАСТЕР СОЗДАНИЯ СОТРУДНИКА ===
    public function createEmployee()
    {
        $departments = Department::with('positions')->get();
        $roles = Role::all();
        
        return view('users.create', compact('departments', 'roles'));
    }

    public function getDepartmentPositions(Department $department)
    {
        return $department->positions;
    }
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'nullable|string',
            'hire_date' => 'required|date',
            'department_id' => 'nullable|exists:departments,id',
            'position_id' => 'nullable|exists:positions,id',
            'is_active' => 'boolean',
            'access_level' => 'in:user,manager,admin',
            'roles' => 'array',
            'roles.*' => 'exists:roles,id'
        ]);
        
        $user->update($validated);
        
        if ($request->has('roles')) {
            $user->roles()->sync($request->roles);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Пользователь успешно обновлен'
        ]);
    }

    public function show($id)
    {
        $user = User::with(['roles', 'department', 'position'])->findOrFail($id);
        
        return response()->json($user);
    }

    public function assignToPosition(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'department_id' => 'required|exists:departments,id',
                'position_id' => 'nullable|exists:positions,id'
            ]);

            $user = User::findOrFail($request->user_id);
            
            $user->update([
                'department_id' => $request->department_id,
                'position_id' => $request->position_id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Сотрудник успешно назначен на должность'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка: ' . $e->getMessage()
            ], 500);
        }
    }

}