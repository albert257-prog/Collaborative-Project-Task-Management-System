<?php

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// 1. Redirect guests to login, or logged-in users to the dashboard
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// 2. Guest Routes (Publicly accessible)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// 3. Protected Routes (Must be logged in to access)
Route::middleware('auth')->group(function () {
    
    // Auth Actions
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [ProjectController::class, 'index'])->name('dashboard');

    // --- Project Management ---
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::patch('/projects/{project}/status', [ProjectController::class, 'toggleStatus'])->name('projects.toggle-status');
    Route::post('/projects/{project}/members', [ProjectController::class, 'addMember'])->name('projects.members.store');
    Route::patch('/projects/{project}/transfer', [ProjectController::class, 'transferOwnership'])->name('projects.transfer');
    Route::post('/projects/{project}/leave', [ProjectController::class, 'leave'])->name('projects.leave');
    Route::delete('/projects/{project}/members/{user}', [ProjectController::class, 'removeMember'])->name('projects.members.destroy');

    // --- Task Management (Requirement 4.4)
    
    // Create task within a project
    Route::post('/projects/{project}/tasks', [TaskController::class, 'store'])->name('tasks.store');
    
    // Claim a task (Needs project context for capacity check)
    Route::post('/projects/{project}/tasks/{task}/claim', [TaskController::class, 'claim'])->name('tasks.claim');

    // Update status (PENDING, IN-PROGRESS, COMPLETED)
    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.update-status');

    // Assign an unassigned task to a specific member (Owner action)
    Route::patch('/tasks/{task}/assign', [TaskController::class, 'assign'])->name('tasks.assign');

    // Delete a task (Assignee or Creator only)
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

    // Alter project status
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');
    Route::patch('/projects/{project}/toggle-status', [ProjectController::class, 'toggleStatus'])->name('projects.toggle-status');
    Route::patch('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
});