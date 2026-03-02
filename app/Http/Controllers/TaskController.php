<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * TaskController manages the task lifecycle within projects.
 * Enforces the "Rule of 3" active tasks and permission-based editing.
 */
class TaskController extends Controller
{
    use AuthorizesRequests;

    /** @var TaskService */
    protected $service;

    /**
     * Dependency injection for task-related business logic.
     */
    public function __construct(TaskService $service)
    {
        $this->service = $service;
    }

    /**
     * Store a new task within a project.
     * Requirement 4.4: Tasks must belong to a project.
     * Optional: Assign to the creator immediately if they have capacity.
     */
    public function store(Request $request, Project $project)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date'    => 'nullable|date|after_or_equal:today',
            'assign_to_me' => 'boolean'
        ]);

        // 1. Capacity Check: Ensure user doesn't exceed 3 active tasks in this project
        if ($request->assign_to_me && $this->isUserFullInProject(auth()->user(), $project)) {
            return back()->with('error', 'You already have 3 active tasks in this project.');
        }

        // 2. Create the task associated with the project
        $project->tasks()->create([
            'title'       => $request->title,
            'description' => $request->description,
            'due_date'    => $request->due_date,
            'status'      => 'PENDING', // Requirement: Default status
            'creator_id'  => auth()->id(),
            'user_id'     => $request->assign_to_me ? auth()->id() : null, // Claiming during creation
        ]);

        return back()->with('success', 'Task created successfully.');
    }

    /**
     * Claim an unassigned task from the project task pool.
     * Requirement 4.4: Participant can claim if they have an available slot (Max 3 active).
     */
    public function claim(Project $project, Task $task)
    {
        // 1. Double check task is actually unassigned
        if ($task->user_id !== null) {
            return back()->with('error', 'This task has already been claimed.');
        }

        // 2. Capacity Check: Enforce the "Rule of 3"
        if ($this->isUserFullInProject(auth()->user(), $project)) {
            return back()->with('error', 'You have reached your maximum of 3 active tasks for this project.');
        }

        // 3. Update task ownership and reset status to PENDING
        $task->update([
            'user_id' => auth()->id(),
            'status'  => 'PENDING'
        ]);

        return back()->with('success', 'Task claimed successfully!');
    }

    /**
     * Update the status of an assigned task.
     * Requirement 4.4: Status must be PENDING, IN-PROGRESS, or COMPLETED.
     * Restriction: Only the assigned participant can update the status.
     */
    public function updateStatus(Request $request, Task $task)
    {
        // 1. Authorization: Verify that the current user is the one assigned to the task
        if (auth()->id() !== $task->user_id) {
            abort(403, 'You can only update tasks assigned to you.');
        }

        // 2. Validation: Strict check for allowed statuses
        $validated = $request->validate([
            'status' => 'required|in:PENDING,IN-PROGRESS,COMPLETED'
        ]);

        // 3. Update task status
        $task->update([
            'status' => $validated['status']
        ]);

        return back()->with('success', 'Task status updated!');
    }

    /**
     * Delete a task from the system.
     * Requirement 4.4: Only the assignee (or creator if unassigned) can delete.
     */
    public function destroy(Task $task)
    {
        // Handled by TaskPolicy to respect Requirement 4.4 ownership rules
        $this->authorize('delete', $task);
        
        $task->delete();
        
        return back()->with('success', 'Task deleted.');
    }

    /**
     * Helper Method: Calculate if a user has reached the "Active Task" limit.
     * Requirement 4.4: Active tasks are those in 'PENDING' or 'IN-PROGRESS' status.
     */
    private function isUserFullInProject(User $user, Project $project)
    {
        $activeCount = $project->tasks()
            ->where('user_id', $user->id)
            ->whereIn('status', ['PENDING', 'IN-PROGRESS'])
            ->count();

        return $activeCount >= 3;
    }
}