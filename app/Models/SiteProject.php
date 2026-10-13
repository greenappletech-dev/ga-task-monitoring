<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteProject extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'project_id',
        'lead_user_id',
        'version',
        'go_live_date',
        'status',
    ];

    protected $casts = [
        'go_live_date' => 'date',
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function lead()
    {
        return $this->belongsTo(User::class, 'lead_user_id');
    }

    public function leads()
    {
        return $this->belongsToMany(User::class, 'site_project_leads', 'site_project_id', 'user_id')
            ->withTimestamps();
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
