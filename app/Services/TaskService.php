<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;

class TaskService
{
    /**
     * Create a task for a project. 
     * Requirement: Each person owns the tasks they create.
     */
    public function create(Project $project, array $data, User $user): Task
    {
        return Task::create([
            'project_id' => $project->id,
            'user_id'    => $user->id, // The creator is the owner
            'title'      => $data['title'],
            'description'=> $data['description'] ?? null,
            'status'     => 'pending'
        ]);
    }

    /**
     * Requirement: No one can interfere with another person’s work.
     * This logic is best enforced in the Controller/Policy, but the service
     * can handle the actual update.
     */
    public function toggleStatus(Task $task): void
    {
        $task->update([
            'status' => $task->status === 'pending' ? 'completed' : 'pending'
        ]);
    }

    public function claim(Task $task, User $user): void
    {
        // Ensure no one else has claimed it first
        if ($task->assigned_to) {
            throw new \Exception('Task already claimed.');
        }
        $task->update(['assigned_to' => $user->id]);
    }
}