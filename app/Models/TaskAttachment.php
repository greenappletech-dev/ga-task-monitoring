<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'comment_id',
        'uat_id',
        'file_name',
        'file_type',
        'file_size',
        'storage_path',
        'uploaded_by',
        'uploaded_at',
        'is_deleted',
        'deleted_by',
        'deleted_at',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
        'deleted_at' => 'datetime',
        'is_deleted' => 'boolean',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function comment()
    {
        return $this->belongsTo(TaskComment::class, 'comment_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_deleted', false);
    }
}
