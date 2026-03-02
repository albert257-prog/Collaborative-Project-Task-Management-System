<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    /**
     * Requirement: Owners can add members, toggle status, and transfer ownership
     */
    /**
     * Requirement: Owners have "Full Control"
     * This includes: Adding members, toggling status, transferring ownership, 
     * editing details, and deleting the project.
     */
    public function manage(User $user, Project $project): bool
    {
        return $user->id === $project->owner_id;
    }

    /**
     * Requirement: Members (Owner + Contributors) can see the project.
     */
    public function view(User $user, Project $project): bool
    {
        return $user->id === $project->owner_id || $project->users->contains($user->id);
    }
}
