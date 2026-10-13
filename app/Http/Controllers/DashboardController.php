<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Site;
use App\Models\Project;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Get task statistics
        $stats = [
            'total_open' => Task::whereNotIn('status', ['Done', 'Rejected', 'Cancelled'])->count(),
            'my_tasks' => $user->hasRole('Developer') ? Task::where('assigned_to', $user->id)
                ->whereNotIn('status', ['Done', 'Rejected', 'Cancelled'])->count() : 0,
            'overdue' => Task::overdue()->count(),
            'due_this_week' => Task::whereBetween('target_date', [Carbon::now(), Carbon::now()->addWeek()])
                ->whereNotIn('status', ['Done', 'Rejected', 'Cancelled'])->count(),
            'new_requests' => Task::where('status', 'New')->count(),
            'blocked' => Task::where('status', 'Blocked')->count(),
        ];

        // Tasks by status
        $tasksByStatus = Task::select('status', \DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Tasks by priority
        $tasksByPriority = Task::select('priority', \DB::raw('count(*) as count'))
            ->whereNotIn('status', ['Done', 'Rejected', 'Cancelled'])
            ->groupBy('priority')
            ->pluck('count', 'priority')
            ->toArray();

        // Recent tasks
        $recentTasks = Task::with(['siteProject.site', 'siteProject.project', 'developer'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // My recent tasks (for developers)
        $myRecentTasks = collect();
        if ($user->hasRole('Developer')) {
            $myRecentTasks = Task::with(['siteProject.site', 'siteProject.project'])
                ->where('assigned_to', $user->id)
                ->orderBy('target_date', 'asc')
                ->limit(5)
                ->get();
        }

        // Overdue tasks (for leads/admins)
        $overdueTasks = collect();
        if ($user->hasAnyRole(['Lead', 'Admin'])) {
            $overdueTasks = Task::with(['siteProject.site', 'siteProject.project', 'developer'])
                ->overdue()
                ->orderBy('target_date', 'asc')
                ->limit(10)
                ->get();
        }

        return view('dashboard', compact(
            'stats',
            'tasksByStatus',
            'tasksByPriority',
            'recentTasks',
            'myRecentTasks',
            'overdueTasks'
        ));
    }
}
