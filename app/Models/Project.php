<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function siteProjects()
    {
        return $this->hasMany(SiteProject::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
