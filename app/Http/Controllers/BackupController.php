<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    /**
     * Проверка прав администратора
     */
    private function checkAdmin()
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            abort(403, 'Доступ запрещен. Требуются права администратора.');
        }
    }

    public function index()
    {
        $this->checkAdmin();
        
        $backupFiles = [];
        $backupPath = storage_path('app/backups');
        
        if (file_exists($backupPath)) {
            $files = scandir($backupPath);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
                    $filePath = $backupPath . '/' . $file;
                    $backupFiles[] = [
                        'name' => $file,
                        'size' => filesize($filePath),
                        'date' => date('Y-m-d H:i:s', filemtime($filePath)),
                        'path' => $filePath
                    ];
                }
            }
            
            // Сортируем по дате (новые сверху)
            usort($backupFiles, function($a, $b) {
                return strtotime($b['date']) - strtotime($a['date']);
            });
        }
        
        return view('backup.index', compact('backupFiles'));
    }
    
    public function createBackup(Request $request)
    {
        $this->checkAdmin();

        try {
            $databaseName = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            $host = config('database.connections.mysql.host');
            
            // Создаем папку для бэкапов если не существует
            $backupPath = storage_path('app/backups');
            if (!file_exists($backupPath)) {
                mkdir($backupPath, 0755, true);
            }
            
            // Формируем имя файла с timestamp
            $filename = 'backup_' . $databaseName . '_' . date('Y-m-d_H-i-s') . '.sql';
            $filepath = $backupPath . '/' . $filename;
            
            // Команда для mysqldump
            $command = "mysqldump --user={$username} --password={$password} --host={$host} {$databaseName} > {$filepath}";
            
            // Выполняем команду
            $output = [];
            $returnCode = 0;
            exec($command . ' 2>&1', $output, $returnCode);
            
            if ($returnCode !== 0) {
                throw new \Exception('Ошибка выполнения mysqldump: ' . implode(', ', $output));
            }
            
            // Проверяем что файл создан и не пустой
            if (!file_exists($filepath) || filesize($filepath) === 0) {
                throw new \Exception('Backup file was not created or is empty');
            }
            
            return redirect()->route('settings.index', ['#backup'])->with('success', 'Бэкап успешно создан: ' . $filename);
            
        } catch (\Exception $e) {
            return redirect()->route('settings.index', ['#backup'])->with('error', 'Ошибка при создании бэкапа: ' . $e->getMessage());
        }
    }
    
    public function downloadBackup($filename)
    {
        $this->checkAdmin();
        
        $filepath = storage_path('app/backups/' . $filename);
        
        if (!file_exists($filepath)) {
            abort(404, 'Файл бэкапа не найден');
        }
        
        return response()->download($filepath);
    }
    
    public function deleteBackup($filename)
    {
        $this->checkAdmin();
        
        $filepath = storage_path('app/backups/' . $filename);
        
        if (!file_exists($filepath)) {
            return redirect()->route('settings.index', ['#backup'])->with('error', 'Файл бэкапа не найден');
        }
        
        unlink($filepath);
        
        return redirect()->route('settings.index', ['#backup'])->with('success', 'Бэкап успешно удален');
    }
    
    public function showRestoreForm()
    {
        $this->checkAdmin();
        
        $backupFiles = [];
        $backupPath = storage_path('app/backups');
        
        if (file_exists($backupPath)) {
            $files = scandir($backupPath);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
                    $backupFiles[] = $file;
                }
            }
        }
        
        return view('backup.restore', compact('backupFiles'));
    }
    
    public function restoreBackup(Request $request)
    {
        $this->checkAdmin();
        
        $request->validate([
            'backup_file' => 'required|string',
            'confirm_database_overwrite' => 'required|accepted',
        ]);
        
        try {
            $filename = $request->backup_file;
            $filepath = storage_path('app/backups/' . $filename);
            
            if (!file_exists($filepath)) {
                return redirect()->route('backup.restore')->with('error', 'Файл бэкапа не найден');
            }
            
            $databaseName = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            $host = config('database.connections.mysql.host');
            
            // Команда для восстановления
            $command = "mysql --user={$username} --password={$password} --host={$host} {$databaseName} < {$filepath}";
            
            // Выполняем команду
            $output = [];
            $returnCode = 0;
            exec($command . ' 2>&1', $output, $returnCode);
            
            if ($returnCode !== 0) {
                throw new \Exception('Ошибка выполнения mysql: ' . implode(', ', $output));
            }
            
            // Очищаем кэш после восстановления
            \Artisan::call('cache:clear');
            \Artisan::call('config:clear');
            
            return redirect()->route('settings.index', ['#backup'])->with('success', 'База данных успешно восстановлена из бэкапа: ' . $filename);
            
        } catch (\Exception $e) {
            return redirect()->route('backup.restore')->with('error', 'Ошибка при восстановлении: ' . $e->getMessage());
        }
    }
    
    public function checkDatabaseExists()
    {
        $this->checkAdmin();
        
        try {
            $databaseName = config('database.connections.mysql.database');
            $result = DB::select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?", [$databaseName]);
            
            return response()->json([
                'exists' => !empty($result),
                'database_name' => $databaseName
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'exists' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
}