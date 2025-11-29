public function assignToPosition(Request $request)
{
    try {
        // Простая валидация
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'nullable|exists:positions,id'
        ]);

        $user = User::findOrFail($request->user_id);
        
        // Обновляем только отдел и должность
        $user->update([
            'department_id' => $request->department_id,
            'position_id' => $request->position_id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Сотрудник успешно назначен на должность'
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Ошибка валидации: ' . implode(', ', $e->errors())
        ], 422);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Ошибка: ' . $e->getMessage()
        ], 500);
    }
}