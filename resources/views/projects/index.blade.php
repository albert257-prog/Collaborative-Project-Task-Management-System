@extends('layouts.app')

@section('content')
<style>
    .main-grid { 
        display: grid; 
        grid-template-columns: 1fr 320px; 
        gap: 30px; 
        max-width: 1250px; 
        margin: 40px auto; 
        padding: 0 20px; 
        align-items: start;
    }

    .sidebar { 
        background: #f8fafc; 
        border-radius: 12px; 
        padding: 20px; 
        border: 1px solid #e2e8f0;
        position: sticky;
        top: 20px;
    }

    .project-card { 
        background: white; border-radius: 12px; padding: 25px; margin-bottom: 25px; 
        box-shadow: 0 4px 15px rgba(0,0,0,0.08); border-top: 5px solid #4a90e2;
    }

    .member-pill {
        display: inline-flex;
        align-items: center;
        background: #edf2f7;
        color: #4a5568;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 0.8em;
        margin-right: 8px;
        margin-bottom: 8px;
        border: 1px solid #e2e8f0;
    }

    .owner-pill {
        background: #ebf4ff;
        border: 1px solid #bee3f8;
        color: #2b6cb0;
        font-weight: bold;
    }

    .remove-member-btn {
        background: none;
        border: none;
        color: #fc8181;
        margin-left: 6px;
        cursor: pointer;
        font-weight: bold;
        padding: 0;
        line-height: 1;
        font-size: 1.1em;
    }

    .remove-member-btn:hover { color: #e53e3e; }

    .my-task-card {
        background: white; padding: 12px; border-radius: 8px; margin-bottom: 10px;
        border-left: 4px solid #4a90e2; box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    
    .flex-between { display: flex; justify-content: space-between; align-items: flex-start; }
    .status-badge { padding: 4px 10px; border-radius: 20px; font-size: 0.7em; font-weight: bold; text-transform: uppercase; }
    .badge-pending { background: #fff3cd; color: #856404; }
    .badge-in-progress { background: #cfe2ff; color: #084298; }
    .badge-completed { background: #d1e7dd; color: #0f5132; }
    
    .task-list { margin-top: 20px; background: #fafafa; border-radius: 8px; padding: 15px; }
    .task-item { display: flex; justify-content: space-between; align-items: center; padding: 12px; border-bottom: 1px solid #eee; font-size: 0.9em; }
    .capacity-tracker { margin-bottom: 15px; padding: 10px 15px; border-radius: 8px; display: flex; align-items: center; font-size: 0.85em; background: #f8fafc; border: 1px solid #e2e8f0; }
    
    .btn { cursor: pointer; border: none; border-radius: 6px; padding: 8px 16px; font-weight: 600; text-decoration: none; display: inline-block; }
    .btn-primary { background: #4a90e2; color: white; }
    .btn-danger { background: #fc8181; color: white; padding: 4px 10px; font-size: 0.8em; }
    .btn-claim { background: #f39c12; color: white; padding: 4px 10px; font-size: 0.8em; }
    .btn-add-member { background: #718096; color: white; padding: 5px 10px; font-size: 0.75em; border-radius: 4px; border: none; }
    .btn-leave { background: #edf2f7; color: #4a5568; padding: 5px 12px; font-size: 0.75em; border: 1px solid #cbd5e0; border-radius: 4px; }
    .btn-leave:hover { background: #fee2e2; color: #991b1b; border-color: #f87171; }

    /* Status-specific colors for the dropdown */
    .status-select {
        width: 100%;
        padding: 6px 10px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        background-color: #f8fafc;
        font-size: 0.85em;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        appearance: none; /* Removes default browser arrow */
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%234a5568'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 10px center;
        background-size: 12px;
    }

    .status-select:hover { border-color: #4a90e2; background-color: #fff; }

    /* Optional: change dropdown color based on selected value */
    .status-select[data-status="PENDING"] { color: #856404; border-left: 4px solid #f6e05e; }
    .status-select[data-status="IN-PROGRESS"] { color: #084298; border-left: 4px solid #4a90e2; }
    .status-select[data-status="COMPLETED"] { color: #0f5132; border-left: 4px solid #48bb78; }
</style>

<div class="main-grid">
    <div class="dashboard-content">
        <header class="flex-between" style="margin-bottom: 30px;">
            <div>
                <h1 style="margin:0;">Collaboration Dashboard</h1>
                <p style="color:#666;">Manage shared workspaces and track team tasks.</p>
            </div>
            <button class="btn btn-primary" onclick="toggleNewProject()">+ Initiate Project</button>
        </header>

        {{-- New Project Form --}}
        <div id="newProjectForm" style="display:none; background: white; padding: 20px; border-radius: 12px; margin-bottom: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <form method="POST" action="{{ route('projects.store') }}">
                @csrf
                <input type="text" name="name" placeholder="Project Name" required style="width:100%; padding:10px; margin-bottom:10px; border:1px solid #ddd; border-radius:6px;">
                <textarea name="description" placeholder="Project Description" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; height:80px;"></textarea>
                <button type="submit" class="btn btn-primary" style="margin-top:10px;">Create Project</button>
            </form>
        </div>

        @forelse($projects as $project)
            <div class="project-card">
                @php
                    $activeCount = $project->tasks()
                    ->where('user_id', auth()->id())
                    ->whereIn('status', ['pending', 'in-progress'])
                    ->count();
                    
                    $isFull = $activeCount >= 3;
                    
                    // Using owner_id to determine ownership
                    $isOwner = auth()->id() === $project->owner_id;
                @endphp

                <div class="flex-between">
                    <div style="flex: 1;">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                            <h2 style="margin:0;">{{ $project->name }}</h2>
                            @if($isOwner)
                                <span style="font-size: 0.7em; color: #2b6cb0; font-weight: bold; background: #ebf4ff; padding: 2px 8px; border-radius: 4px; border: 1px solid #bee3f8;">⭐ OWNER</span>
                            @endif
                        </div>
                        <p style="color: #666; font-size: 0.9em;">{{ $project->description }}</p>
                        
                        {{-- Members List --}}
                        <div style="margin: 15px 0; display: flex; flex-wrap: wrap; align-items: center;">
                            <div class="member-pill owner-pill" title="Project Creator">
                                👤 {{ $project->owner->name ?? 'Unknown Owner' }} (Owner)
                            </div>

                            @foreach($project->users as $user)
                                <div class="member-pill">
                                    <span>{{ $user->name }}</span>
                                    @if($isOwner && $user->id !== auth()->id())
                                        <form action="{{ route('projects.members.destroy', [$project, $user]) }}" method="POST" style="display:inline;">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="remove-member-btn" title="Remove Member">×</button>
                                        </form>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        {{-- Management Section --}}
                        <div style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap; margin-top: 15px;">
                            @if($isOwner)
                                {{-- Add Member --}}
                                <form method="POST" action="{{ route('projects.members.store', $project) }}" style="display:flex; gap:5px; width: 250px;">
                                    @csrf
                                    <input type="email" name="email" placeholder="Add by email..." required 
                                           style="flex-grow:1; font-size: 0.8em; padding: 5px; border-radius:4px; border:1px solid #cbd5e0;">
                                    <button type="submit" class="btn btn-add-member">Add</button>
                                </form>

                                {{-- Transfer Ownership --}}
                                <form method="POST" action="{{ route('projects.transfer', $project) }}" style="display:flex; gap:5px; align-items: center;">
                                    @csrf
                                    @method('PATCH')
                                    <label style="font-size: 0.75em; color: #718096;">Transfer:</label>
                                    <select name="new_owner_id" onchange="if(confirm('Transfer ownership? You will become a regular member.')) this.form.submit()" 
                                            style="font-size: 0.8em; padding: 4px; border-radius: 4px; border: 1px solid #cbd5e0;">
                                        <option value="">Select Member...</option>
                                        @foreach($project->users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            @else
                                
                                <form method="POST" action="{{ route('projects.leave', $project) }}" onsubmit="return confirm('Are you sure you want to leave this project?')">
                                    @csrf 
                                    <button type="submit" class="btn-leave">Exit Project</button>
                                </form>
                            @endif
                        </div>
                    </div>
                    <span class="status-badge badge-{{ $project->status === 'ongoing' ? 'in-progress' : 'completed' }}">
                        {{ $project->status }}
                    </span>
                </div>

                {{-- Capacity Tracker --}}
                <div class="capacity-tracker" style="margin-top:20px; border-left: 4px solid {{ $isFull ? '#fc8181' : '#4a90e2' }}">
                    <span style="margin-right: 10px;">Your Active Slots:</span>
                    @for($i = 1; $i <= 3; $i++)
                        <span style="color: {{ $i <= $activeCount ? '#4a90e2' : '#cbd5e0' }}; font-size: 1.5em; line-height: 1;">●</span>
                    @endfor
                    <small style="margin-left: 10px; font-weight: bold;">({{ $activeCount }}/3)</small>
                </div>

                {{-- Task List --}}
                <div class="task-list">
                    @foreach($project->tasks as $task)
                        <div class="task-item">
                            <div>
                                <strong>{{ $task->title }}</strong>
                                <span class="status-badge badge-{{ $task->status }}" style="margin-left: 10px; font-size: 0.6em;">{{ $task->status }}</span>
                                @if($task->user)
                                    <small style="color: #a0aec0; margin-left: 8px;">— {{ $task->user->name }}</small>
                                @endif
                            </div>
                            <div style="display: flex; gap: 5px;">
                                @if(!$task->user_id)
                                    <form action="{{ route('tasks.claim', [$project, $task]) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-claim" {{ $isFull ? 'disabled' : '' }}>Claim</button>
                                    </form>
                                @endif
                                @can('delete', $task)
                                    <form action="{{ route('tasks.destroy', $task) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                @endcan
                            </div>
                        </div>
                    @endforeach

                    <form method="POST" action="/projects/{{ $project->id }}/tasks" style="margin-top:15px; display:flex; gap:10px;">
                        @csrf
                        <input type="text" name="title" placeholder="Assign a new task..." required style="flex-grow:1; padding:8px; border-radius:6px; border:1px solid #ddd;">
                        <button type="submit" class="btn btn-primary" style="padding:8px 12px;">Add</button>
                    </form>
                </div>
            </div>
        @empty
            <p style="text-align: center; color: #999; margin-top: 50px;">You are not part of any projects yet.</p>
        @endforelse
    </div>

    {{-- Sidebar --}}
    <aside class="sidebar">
        <h3 style="margin-top: 0; color: #2d3748;">🎯 My Active Workload</h3>
        <p style="font-size: 0.8em; color: #718096; margin-bottom: 20px;">Tasks assigned to you across all projects.</p>

        @forelse($myTasks as $myTask)
            <div class="my-task-card">
                <small style="color:#4a90e2; font-weight:bold; display:block;">{{ $myTask->project->name }}</small>
                <p style="margin: 5px 0; font-weight: 600;">{{ $myTask->title }}</p>
                <div style="margin-top: 10px;">
                    <form method="POST" action="{{ route('tasks.update-status', $myTask) }}">
                        @csrf @method('PATCH')
                        <select name="status" onchange="this.form.submit()">
                            <option value="PENDING" {{ $myTask->status === 'PENDING' ? 'selected' : '' }}>⏳ Pending</option>
                            <option value="IN-PROGRESS" {{ $myTask->status === 'IN-PROGRESS' ? 'selected' : '' }}>🚀 In-Progress</option>
                            <option value="COMPLETED" {{ $myTask->status === 'COMPLETED' ? 'selected' : '' }}>✅ Done</option>
                        </select>
                    </form>
                </div>
            </div>
        @empty
            <div style="text-align: center; color: #cbd5e0; padding: 20px;">
                <p style="font-size: 2em; margin: 0;">🎉</p>
                <p>All caught up!</p>
            </div>
        @endforelse
    </aside>
</div>

<script>
    function toggleNewProject() {
        const form = document.getElementById('newProjectForm');
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
    }
</script>
@endsection