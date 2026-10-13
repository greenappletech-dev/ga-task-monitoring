<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'client',
        'location',
        'contact_person',
        'contact_email',
        'contact_phone',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function siteProjects()
    {
        return $this->hasMany(SiteProject::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_site_access');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
