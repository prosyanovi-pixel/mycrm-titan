<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClientFilesController extends Controller // исправлено название
{
    public function index(Client $client)
    {
        // Метод для отображения списка файлов (если нужен отдельно)
        return view('clients.files.index', compact('client'));
    }

    public function store(Request $request, Client $client)
    {
        $request->validate([
            'file' => 'required|file|max:20480', // 20MB limit
            'custom_name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'tags' => 'nullable|string|max:500',
        ]);

        $file = $request->file('file');
        
        // Используем ваш метод для определения типа файла
        $type = ClientFile::getFileType($file);
        
        // Обрабатываем теги
        $tags = [];
        if ($request->tags) {
            $tags = array_map('trim', explode(',', $request->tags));
        }

        // Сохраняем файл
        $filePath = $file->store('client-files', 'public');

        // Создаем запись в базе данных с правильными названиями полей
        $clientFile = $client->files()->create([
            'original_name' => $file->getClientOriginalName(),
            'custom_name' => $request->custom_name,
            'filepath' => $filePath,
            'size' => $file->getSize(),
            'type' => $type,
            'mime_type' => $file->getMimeType(),
            'description' => $request->description,
            'tags' => $tags,
            'uploaded_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Файл успешно загружен');
    }

    public function update(Request $request, Client $client, ClientFile $file)
    {
        $request->validate([
            'custom_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'tags' => 'nullable|string|max:500',
        ]);

        // Обрабатываем теги
        $tags = [];
        if ($request->tags) {
            $tags = array_map('trim', explode(',', $request->tags));
        }

        $file->update([
            'custom_name' => $request->custom_name,
            'description' => $request->description,
            'tags' => $tags,
        ]);

        return redirect()->back()->with('success', 'Файл успешно обновлен');
    }

    public function destroy(Client $client, ClientFile $file)
    {
        // Удаляем файл из хранилища
        Storage::disk('public')->delete($file->filepath);
        
        // Удаляем запись из базы данных
        $file->delete();

        return redirect()->back()->with('success', 'Файл успешно удален');
    }

    public function download(Client $client, ClientFile $file)
    {
        if (!Storage::disk('public')->exists($file->filepath)) {
            abort(404);
        }

        // Получаем оригинальное расширение файла
        $originalExtension = pathinfo($file->original_name, PATHINFO_EXTENSION);
        
        // Формируем имя для скачивания с правильным расширением
        $downloadName = $file->custom_name ?: pathinfo($file->original_name, PATHINFO_FILENAME);
        
        // Добавляем расширение, если его нет
        if (!empty($originalExtension) && !str_contains($downloadName, '.' . $originalExtension)) {
            $downloadName .= '.' . $originalExtension;
        }

        // Устанавливаем правильные заголовки
        $headers = [
            'Content-Type' => $file->mime_type,
            'Content-Disposition' => 'attachment; filename="' . $downloadName . '"',
        ];

        return Storage::disk('public')->download($file->filepath, $downloadName, $headers);
    }
}