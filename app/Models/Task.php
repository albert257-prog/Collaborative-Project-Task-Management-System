<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    /**
     * Requirement 4.4 updates:
     * - Added creator_id to track who made the task.
     * - Added due_date for project planning.
     * - user_id is now specifically the 'Assignee'.
     */
    protected $fillable = [
        'project_id',
        'creator_id',
        'user_id', 
        'title',
        'description', 
        'due_date',
        'status',
    ];

    /**
     * Ensure the due_date is treated as a Carbon instance
     */
    protected $casts = [
        'due_date' => 'date',
    ];

    /**
     * The project this task belongs to.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Requirement 4.4: The participant currently assigned to the task.
     * Returns null if the task is in the 'unassigned pool'.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Requirement 4.4: The participant who originally created the task.
     * Used for editing/deleting permissions before a task is claimed.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function isActive(): bool
    {
        return $this->user_id !== null && in_array($this->status, ['PENDING', 'IN-PROGRESS']);
    }

}