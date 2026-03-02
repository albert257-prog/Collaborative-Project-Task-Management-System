<?php

use App\Models\GlobalTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/global-tasks', function (Request $request) {
    // Basic validation is always a good idea even in bonus routes!
    $data = $request->validate([
        'title' => 'required|string',
        'description' => 'nullable|string'
    ]);

    return GlobalTask::create($data);
});