<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DepartmentPermission extends Model
{
    protected $fillable = [
        'source_department_id', 'target_department_id', 'permission_type', 
        'can_interact', 'can_control'
    ];

    protected $casts = [
        'can_interact' => 'boolean',
        'can_control' => 'boolean'
    ];

    public function sourceDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'source_department_id');
    }

    public function targetDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'target_department_id');
    }
}