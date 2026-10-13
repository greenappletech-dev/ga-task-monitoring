<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Checking tasks in 'For QA UAT' status:\n";
echo str_repeat('-', 60) . "\n";

$tasks = App\Models\Task::where('status', 'For QA UAT')
    ->with(['developer', 'qa'])
    ->get(['id', 'task_no', 'title', 'assigned_to', 'qa_assigned_to']);

if ($tasks->count() === 0) {
    echo "No tasks found in 'For QA UAT' status.\n";
} else {
    foreach ($tasks as $task) {
        echo "Task: {$task->task_no}\n";
        echo "Title: {$task->title}\n";
        echo "Developer: " . ($task->developer ? $task->developer->name : "NOT ASSIGNED") . "\n";
        echo "QA: " . ($task->qa ? $task->qa->name : "NOT ASSIGNED") . "\n";
        echo "Can fail QA? " . ($task->assigned_to ? "YES" : "NO - Missing developer") . "\n";
        echo str_repeat('-', 60) . "\n";
    }
}

// Check QA users
echo "\nChecking QA users:\n";
echo str_repeat('-', 60) . "\n";
$qaUsers = App\Models\User::whereJsonContains('roles', 'QA')
    ->where('is_active', true)
    ->get(['id', 'name', 'email']);

if ($qaUsers->count() === 0) {
    echo "WARNING: No active QA users found!\n";
    echo "You need to create at least one QA user to test the feature.\n";
} else {
    foreach ($qaUsers as $qa) {
        echo "- {$qa->name} ({$qa->email})\n";
    }
}

echo "\n" . str_repeat('=', 60) . "\n";
echo "Summary:\n";
echo "- Tasks in QA UAT: {$tasks->count()}\n";
echo "- Tasks with developer: " . $tasks->where('assigned_to', '!=', null)->count() . "\n";
echo "- Active QA users: {$qaUsers->count()}\n";
echo str_repeat('=', 60) . "\n";
