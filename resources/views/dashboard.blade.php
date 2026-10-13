@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@push('styles')
<style>
    .stat-card-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .stat-card-primary {
        background: linear-gradient(135deg, rgba(139, 197, 63, 0.1) 0%, rgba(45, 139, 80, 0.1) 100%);
        color: #8BC53F;
    }

    .stat-card-success {
        background: linear-gradient(135deg, rgba(139, 197, 63, 0.15) 0%, rgba(45, 139, 80, 0.15) 100%);
        color: #2D8B50;
    }

    .stat-card-danger {
        background: linear-gradient(135deg, rgba(229, 72, 77, 0.1) 0%, rgba(220, 38, 38, 0.1) 100%);
        color: #E5484D;
    }

    .stat-card-warning {
        background: linear-gradient(135deg, rgba(242, 128, 43, 0.1) 0%, rgba(251, 146, 60, 0.1) 100%);
        color: #F2802B;
    }

    .stat-card-info {
        background: linear-gradient(135deg, rgba(139, 197, 63, 0.08) 0%, rgba(45, 139, 80, 0.08) 100%);
        color: #8BC53F;
    }

    .stat-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12) !important;
    }

    .card-header {
        border-bottom: 2px solid #8BC53F;
        background: linear-gradient(135deg, rgba(139, 197, 63, 0.05) 0%, rgba(45, 139, 80, 0.05) 100%);
    }

    .list-group-item {
        border-color: #E2E5EA;
    }

    .badge.bg-primary {
        background: linear-gradient(135deg, #8BC53F 0%, #2D8B50 100%) !important;
    }

    .btn-outline-primary:hover {
        background: linear-gradient(135deg, #8BC53F 0%, #2D8B50 100%);
        border-color: #8BC53F;
    }
</style>
@endpush

@section('content')
<div class="row g-3 mb-4">
    <!-- Stats Cards -->
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-secondary small mb-2">Total Open</div>
                        <h3 class="mb-0 fw-bold">{{ $stats['total_open'] }}</h3>
                    </div>
                    <div class="stat-card-icon stat-card-primary">
                        <i class="bi bi-list-task"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(auth()->user()->hasRole('Developer'))
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-secondary small mb-2">My Tasks</div>
                        <h3 class="mb-0 fw-bold">{{ $stats['my_tasks'] }}</h3>
                    </div>
                    <div class="stat-card-icon stat-card-success">
                        <i class="bi bi-person-check"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-secondary small mb-2">Overdue</div>
                        <h3 class="mb-0 fw-bold text-danger">{{ $stats['overdue'] }}</h3>
                    </div>
                    <div class="stat-card-icon stat-card-danger">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-secondary small mb-2">Due This Week</div>
                        <h3 class="mb-0 fw-bold text-warning">{{ $stats['due_this_week'] }}</h3>
                    </div>
                    <div class="stat-card-icon stat-card-warning">
                        <i class="bi bi-calendar-week"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-secondary small mb-2">New Requests</div>
                        <h3 class="mb-0 fw-bold">{{ $stats['new_requests'] }}</h3>
                    </div>
                    <div class="stat-card-icon stat-card-info">
                        <i class="bi bi-inbox"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-secondary small mb-2">Blocked</div>
                        <h3 class="mb-0 fw-bold text-danger">{{ $stats['blocked'] }}</h3>
                    </div>
                    <div class="stat-card-icon stat-card-danger">
                        <i class="bi bi-slash-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Tasks by Status -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Tasks by Status</h5>
            </div>
            <div class="card-body">
                @if(count($tasksByStatus) > 0)
                <div class="list-group list-group-flush">
                    @foreach($tasksByStatus as $status => $count)
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <span>{{ $status }}</span>
                        <span class="badge bg-primary rounded-pill">{{ $count }}</span>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-muted">No tasks yet.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Tasks by Priority -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Open Tasks by Priority</h5>
            </div>
            <div class="card-body">
                @if(count($tasksByPriority) > 0)
                <div class="list-group list-group-flush">
                    @foreach(['Urgent', 'High', 'Medium', 'Low'] as $priority)
                        @if(isset($tasksByPriority[$priority]))
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span>{{ $priority }}</span>
                            <span class="badge badge-{{ strtolower($priority) }} rounded-pill">{{ $tasksByPriority[$priority] }}</span>
                        </div>
                        @endif
                    @endforeach
                </div>
                @else
                <p class="text-muted">No open tasks.</p>
                @endif
            </div>
        </div>
    </div>

    @if(auth()->user()->hasRole('Developer') && $myRecentTasks->count() > 0)
    <!-- My Tasks -->
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">My Upcoming Tasks</h5>
                <a href="{{ route('tasks.index') }}?assigned_to=me" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Task ID</th>
                                <th>Title</th>
                                <th>Site · Project</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Target Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($myRecentTasks as $task)
                            <tr>
                                <td><a href="{{ route('tasks.show', $task) }}">{{ $task->task_no }}</a></td>
                                <td>{{ Str::limit($task->title, 50) }}</td>
                                <td>{{ $task->siteProject->site->name }} · {{ $task->siteProject->project->name }}</td>
                                <td><span class="badge badge-{{ strtolower($task->priority) }}">{{ $task->priority }}</span></td>
                                <td><span class="badge bg-secondary">{{ $task->status }}</span></td>
                                <td>
                                    @if($task->target_date)
                                        {{ $task->target_date->format('M d, Y') }}
                                        @if($task->target_date->isPast())
                                            <i class="bi bi-exclamation-circle text-danger"></i>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(auth()->user()->hasAnyRole(['Lead', 'Admin']) && $overdueTasks->count() > 0)
    <!-- Overdue Tasks -->
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-danger"><i class="bi bi-exclamation-triangle"></i> Overdue Tasks</h5>
                <a href="{{ route('tasks.index') }}?overdue=1" class="btn btn-sm btn-outline-danger">View All</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Task ID</th>
                                <th>Title</th>
                                <th>Site · Project</th>
                                <th>Assigned To</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Target Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($overdueTasks as $task)
                            <tr>
                                <td><a href="{{ route('tasks.show', $task) }}">{{ $task->task_no }}</a></td>
                                <td>{{ Str::limit($task->title, 40) }}</td>
                                <td>{{ $task->siteProject->site->name }} · {{ $task->siteProject->project->name }}</td>
                                <td>{{ $task->developer ? $task->developer->name : 'Unassigned' }}</td>
                                <td><span class="badge badge-{{ strtolower($task->priority) }}">{{ $task->priority }}</span></td>
                                <td><span class="badge bg-secondary">{{ $task->status }}</span></td>
                                <td class="text-danger">
                                    {{ $task->target_date->format('M d, Y') }}
                                    ({{ $task->target_date->diffForHumans() }})
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Recent Tasks -->
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recent Tasks</h5>
                <a href="{{ route('tasks.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body">
                @if($recentTasks->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Task ID</th>
                                <th>Title</th>
                                <th>Site · Project</th>
                                <th>Assigned To</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentTasks as $task)
                            <tr>
                                <td><a href="{{ route('tasks.show', $task) }}">{{ $task->task_no }}</a></td>
                                <td>{{ Str::limit($task->title, 40) }}</td>
                                <td>{{ $task->siteProject->site->name }} · {{ $task->siteProject->project->name }}</td>
                                <td>{{ $task->developer ? $task->developer->name : 'Unassigned' }}</td>
                                <td><span class="badge badge-{{ strtolower($task->priority) }}">{{ $task->priority }}</span></td>
                                <td><span class="badge bg-secondary">{{ $task->status }}</span></td>
                                <td>{{ $task->created_at->diffForHumans() }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted">No tasks yet. <a href="{{ route('tasks.create') }}">Create one now</a>.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
