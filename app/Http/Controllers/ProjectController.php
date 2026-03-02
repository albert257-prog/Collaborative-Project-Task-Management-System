<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use App\Models\Task; 
use App\Services\ProjectService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Mail;
use App\Mail\ProjectAddedNotification;

/**
 * ProjectController handles the lifecycle of a Project, including
 * membership, ownership transfers, and the main dashboard view.
 */
class ProjectController extends Controller
{
    use AuthorizesRequests;

    /** @var ProjectService */
    protected $service;

    /**
     * Inject ProjectService for business logic encapsulation.
     */
    public function __construct(ProjectService $service)
    {
        $this->service = $service;
    }

    /**
     * Display the main dashboard (Project Index).
     * Requirements: Shows projects where user is involved and a personal task sidebar.
     */
    public function index()
    {
        $user = auth()->user();

        // 1. Calculate global workload (Across ALL projects) for the (0/3) counter
        $globalActiveCount = Task::where('user_id', $user->id)
            ->whereIn('status', ['PENDING', 'IN-PROGRESS'])
            ->count();

        // 2. Fetch only projects where the user is the owner OR a collaborator
        $projects = Project::where('owner_id', $user->id)
            ->orWhereHas('users', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            })
            ->with(['tasks', 'users', 'owner'])
            ->get();

        // 3. Fetch tasks specifically assigned to the logged-in user for the sidebar
        $myTasks = Task::where('user_id', $user->id)
            ->whereIn('status', ['PENDING', 'IN-PROGRESS'])
            ->with('project')
            ->get();

        return view('projects.index', compact('projects', 'myTasks', 'globalActiveCount'));
    }

    /**
     * Store a newly created project.
     * Requirement: Initiates project and assigns the current user as the owner.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);
        
        $this->service->create($request->all(), auth()->user());
        
        return back()->with('success', 'Project initiated!');
    }

    /**
     * Add a member to a project via email.
     */
    public function addMember(Request $request, Project $project)
    {
        // Authorize via ProjectPolicy (manage method)
        $this->authorize('manage', $project);

        $request->validate(['email' => 'required|email|exists:users,email']);

        // Check Capacity: Limit of 3 contributors besides the owner
        if ($project->users()->count() >= 4) {
            return back()->with('error', 'Project limit reached (4 contributors max).');
        }

        $userToAdd = User::where('email', $request->email)->first();

        // Business Rule: Owner cannot be added as a member
        if ($userToAdd->id === $project->owner_id) {
            return back()->with('error', 'You are already the owner.');
        }

        // Business Rule: Prevent duplicate entries
        if ($project->users()->where('users.id', $userToAdd->id)->exists()) {
            return back()->with('error', 'This user is already a member.');
        }

        $project->users()->attach($userToAdd->id);

        // Attempt to notify the user via Email
        try {
            Mail::to($userToAdd->email)->send(new ProjectAddedNotification($project, auth()->user()->name));
        } catch (\Exception $e) {
            // Log error if needed, but don't stop the request for email failures
        }

        return back()->with('success', 'Contributor added successfully.');
    }

    /**
     * Transfer project ownership to another member.
     * Requirement: Old owner becomes a contributor; new owner takes the lead.
     */
    public function transferOwnership(Request $request, Project $project)
    {
        $this->authorize('manage', $project);

        $request->validate(['new_owner_id' => 'required|exists:users,id']);

        $oldOwnerId = $project->owner_id;
        $newOwnerId = $request->new_owner_id;

        // Update Project record
        $project->update(['owner_id' => $newOwnerId]);

        // Logic: Add old owner to contributors list, remove new owner from that list
        $project->users()->syncWithoutDetaching([$oldOwnerId]);
        $project->users()->detach($newOwnerId);

        return back()->with('success', 'Ownership transferred! You are now a contributor.');
    }

    /**
     * Remove a member from the project.
     * Requirement 4.5: Retain data by unassigning tasks instead of deleting them.
     */
    public function removeMember(Project $project, User $user)
    {
        // Ensure we aren't removing the owner
        if ($user->id === $project->owner_id) {
            return back()->with('error', 'The owner cannot be removed.');
        }

        // Data Retention: Release active tasks back to the unassigned pool
        $project->tasks()
            ->where('user_id', $user->id)
            ->whereIn('status', ['PENDING', 'IN-PROGRESS'])
            ->update(['user_id' => null]);

        $project->users()->detach($user->id);

        return back()->with('success', "{$user->name} removed. Their tasks are now unassigned.");
    }

    /**
     * Allow a contributor to leave the project voluntarily.
     */
    public function leave(Project $project)
    {
        if ($project->owner_id === auth()->id()) {
            return back()->with('error', 'Owners cannot leave. Transfer ownership first.');
        }

        // Clean up: Release their assigned tasks before they leave
        Task::where('project_id', $project->id)
            ->where('user_id', auth()->id())
            ->update(['user_id' => null]);

        $project->users()->detach(auth()->id());
        
        return redirect()->route('dashboard')->with('success', 'You have left the project.');
    }

    /**
     * Toggle project status between 'ongoing' and 'completed'.
     */
    /**
     * Requirement: Owner has full control to edit project details.
     */
    public function update(Request $request, Project $project)
    {
        $this->authorize('manage', $project);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $project->update($request->only('name', 'description'));

        return back()->with('success', 'Project details updated successfully.');
    }

    /**
     * Requirement: Owner has full control to delete the project.
     */
    public function destroy(Project $project)
    {
        $this->authorize('manage', $project);

        // Optional: Perform cleanup or cascade soft deletes if necessary
        $project->delete();

        return redirect()->route('dashboard')->with('success', 'Project deleted successfully.');
    }

    /**
     * Toggle project status between 'Active' and 'Completed'.
     * Only the owner has full control over this lifecycle.
     */
    public function toggleStatus(Project $project)
    {
        $this->authorize('manage', $project);

        // Change 'Active' to 'ongoing' to match your database constraints
        $newStatus = (strtolower($project->status) === 'ongoing') ? 'completed' : 'ongoing';

        $project->update(['status' => $newStatus]);

        return back()->with('success', 'Project status updated!');
    }
}