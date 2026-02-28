<?php

namespace App\Services;

use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Exception;

class ProjectService
{
    /**
     * Requirement: Project initiated by a single user who becomes its owner.
     */
    public function create(array $data, User $user): Project
    {
        return DB::transaction(function () use ($data, $user) {
            $project = Project::create([
                'name'        => $data['name'],
                'description' => $data['description'] ?? null,
                'owner_id'    => $user->id,
                'status'      => 'ongoing', // Ensure this matches your migration enum!
            ]);

            // Requirement: Owner is also a member who can contribute tasks.
            $project->users()->attach($user->id);

            return $project;
        });
    }

    /**
     * Requirement: Owner can bring other registered users in as members.
     */
    public function addMember(Project $project, User $user): void
    {
        // Optimized check: Queries the database directly instead of the loaded collection
        if ($project->users()->where('user_id', $user->id)->exists()) {
            throw new Exception('User is already a member of this project.');
        }

        $project->users()->attach($user->id);
    }

    public function transferOwnership(Project $project, User $newOwner): void
    {
        // Update the owner_id to the new member
        $project->update(['owner_id' => $newOwner->id]);
    }
}