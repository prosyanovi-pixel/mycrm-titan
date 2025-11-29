<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientFile extends Model
{
    protected $fillable = [
        'client_id',
        'uploaded_by', // исправлено с user_id
        'original_name',
        'custom_name',
        'filepath',    // исправлено с filename
        'description',
        'tags',
        'mime_type',
        'size',
        'type'
    ];

    protected $casts = [
        'tags' => 'array',
        'size' => 'integer'
    ];

    // Добавляем accessor для отображаемого имени
    public function getDisplayNameAttribute()
    {
        return $this->custom_name ?: $this->original_name;
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'uploaded_by'); // исправлено
    }

    // Метод для определения типа файла
    public static function getFileType($file)
    {
        $mime = $file->getMimeType();
        $extension = strtolower($file->getClientOriginalExtension());

        if (str_contains($mime, 'image')) {
            return 'image';
        } elseif (in_array($extension, ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx'])) {
            return 'document';
        } elseif (in_array($extension, ['zip', 'rar', 'tar', 'gz', '7z'])) {
            return 'archive';
        } else {
            return 'other';
        }
    }
}