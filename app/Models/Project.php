<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    /**
     * Check if the authenticated user has reached their task limit within this project.
     * This removes the logic from the Blade file.
     */
    public function getIsUserFullAttribute()
    {
        return $this->tasks()
            ->where('user_id', auth()->id())
            ->whereIn('status', ['PENDING', 'IN-PROGRESS'])
            ->count() >= 3;
    }

    // Ensure 'status' is fillable to track ongoing vs completed
    protected $fillable = ['name', 'description', 'status', 'owner_id'];

    /**
     * Requirement: A project is initiated by a single user who becomes its owner.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Requirement: The owner can bring other registered users in as members.
     * We use 'users' to match the 'project_user' table name.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_user')
                    ->withTimestamps();
    }

    /**
     * Requirement: Everyone in the project should have full visibility into tasks.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}