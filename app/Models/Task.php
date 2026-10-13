<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_no', 'site_project_id', 'type', 'title', 'description',
        'source', 'priority', 'status', 'requested_by', 'date_requested',
        'client_contact', 'assigned_to', 'qa_assigned_to', 'date_assigned',
        'target_date', 'sla_due_date', 'blocked_reason', 'blocked_at',
        'blocked_duration_hours', 'completed_date', 'percent_complete',
        'est_effort', 'actual_effort', 'return_count',
    ];

    protected $casts = [
        'date_requested' => 'date',
        'date_assigned' => 'date',
        'target_date' => 'date',
        'sla_due_date' => 'date',
        'completed_date' => 'date',
        'blocked_at' => 'datetime',
        'percent_complete' => 'integer',
        'return_count' => 'integer',
    ];

    public function siteProject()
    {
        return $this->belongsTo(SiteProject::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function developer()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function qa()
    {
        return $this->belongsTo(User::class, 'qa_assigned_to');
    }

    public function comments()
    {
        return $this->hasMany(TaskComment::class)->orderBy('created_at', 'desc');
    }

    // public function attachments()
    // {
    //     return $this->hasMany(TaskAttachment::class)->where('is_deleted', false);
    // }

    // public function history()
    // {
    //     return $this->hasMany(TaskHistory::class)->orderBy('changed_at', 'desc');
    // }

    // public function uatResults()
    // {
    //     return $this->hasMany(TaskUat::class);
    // }

    // public function targetDateRequests()
    // {
    //     return $this->hasMany(TargetDateRequest::class);
    // }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeOverdue($query)
    {
        return $query->where('target_date', '<', now())
            ->whereNotIn('status', ['Done', 'Rejected', 'Cancelled']);
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }
}
