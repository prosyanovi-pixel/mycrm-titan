<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::with(['manager', 'children', 'users'])->get();
        return view('admin.departments.index', compact('departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:departments,id',
            'manager_id' => 'nullable|exists:users,id',
        ]);

        Department::create($validated);
        
        return redirect()->route('settings.index', ['tab' => 'users-roles'])->with('success', 'Отдел создан!');
    }

    // Показать данные отдела для редактирования (API)
    public function show(Department $department)
    {
        return response()->json($department);
    }

    // Обновить отдел
    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:departments,id',
            'manager_id' => 'nullable|exists:users,id',
        ]);

        $department->update($validated);
        
        return redirect()->route('settings.index', ['tab' => 'users-roles'])->with('success', 'Отдел обновлен!');
    }

    public function destroy(Department $department)
    {
        if ($department->users()->exists()) {
            return response()->json(['success' => false, 'message' => 'Нельзя удалить отдел с сотрудниками'], 403);
        }

        $department->delete();
        return response()->json(['success' => true]);
    }

    // === ДОЛЖНОСТИ ===
    public function storePosition(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'parent_position_id' => 'nullable|exists:positions,id',
            'is_manager' => 'boolean',
        ]);

        Position::create($validated);
        
        return redirect()->route('settings.index', ['tab' => 'users-roles'])->with('success', 'Должность создана!');
    }

    // Показать данные должности для редактирования (API)
    public function showPosition(Position $position)
    {
        return response()->json($position);
    }

    // Обновить должность
    public function updatePosition(Request $request, $id)
    {
        $position = Position::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'parent_position_id' => 'nullable|exists:positions,id',
            'is_manager' => 'boolean'
        ]);
        
        $position->update($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Должность успешно обновлена'
        ]);
    }

    public function destroyPosition(Position $position)
    {
        if ($position->users()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Нельзя удалить должность с сотрудниками'
            ], 403);
        }

        $position->delete();
        return response()->json(['success' => true]);
    }
}