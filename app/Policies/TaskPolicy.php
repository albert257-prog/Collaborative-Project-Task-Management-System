<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * Requirement 4.4: Only the assignee can edit/update status.
     * Unassigned tasks can be edited by the creator until claimed.
     */
    public function update(User $user, Task $task)
    {
        // If assigned, only the assignee can update
        if ($task->user_id) {
            return $user->id === $task->user_id;
        }
        // If unassigned, only the project owner (creator) can edit/delete
        return $user->id === $task->project->owner_id;
    }

    public function delete(User $user, Task $task)
    {
        return $this->update($user, $task);
    }

    /**
     * Requirement 4.4: Any project participant can claim if they have a slot.
     * (Note: The project membership check is usually handled by the Controller or a ProjectPolicy)
     */
    public function claim(User $user, Task $task): bool
    {
        // 1. Task must be unassigned
        // 2. User must have fewer than 3 active tasks in THIS project
        $activeCount = $task->project->tasks()
            ->where('user_id', $user->id)
            ->whereIn('status', ['PENDING', 'IN-PROGRESS'])
            ->count();

        return is_null($task->user_id) && $activeCount < 3;
    }
}