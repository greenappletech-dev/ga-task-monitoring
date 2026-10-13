@extends('layouts.admin')

@section('title', 'Site Project Details')
@section('page-title', 'Site Project Details')

@push('styles')
<style>
    @media (max-width: 768px) {
        .col-md-8,
        .col-md-4 {
            padding-left: 0;
            padding-right: 0;
        }

        .card-body {
            padding: 1rem;
        }

        .btn {
            width: 100%;
            margin-bottom: 0.5rem;
        }

        .d-flex.gap-2 {
            flex-direction: column;
            gap: 0.5rem !important;
        }
    }
</style>
@endpush

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center">
    <a href="{{ route('admin.site-projects.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to List
    </a>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.site-projects.edit', $siteProject) }}" class="btn btn-sm btn-primary">
            <i class="bi bi-pencil"></i> Edit
        </a>
        <form action="{{ route('admin.site-projects.destroy', $siteProject) }}" method="POST" class="d-inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-danger"
                    onclick="return confirm('Are you sure you want to delete this site project?')">
                <i class="bi bi-trash"></i> Delete
            </button>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <!-- Main Details Card -->
        <div class="card mb-3">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="bi bi-link-45deg"></i> Site Project Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="small text-muted mb-1">Site</p>
                        <h6>{{ $siteProject->site->name }}</h6>
                        <p class="text-muted small mb-0">
                            <i class="bi bi-building"></i> {{ $siteProject->site->code }}
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="small text-muted mb-1">Project</p>
                        <h6>{{ $siteProject->project->name }}</h6>
                        @if($siteProject->project->description)
                        <p class="text-muted small mb-0">{{ $siteProject->project->description }}</p>
                        @endif
                    </div>
                </div>

                <hr>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <p class="small text-muted mb-1">Version</p>
                        <p class="mb-0">
                            @if($siteProject->version)
                                <span class="badge bg-info">v{{ $siteProject->version }}</span>
                            @else
                                <span class="text-muted">Not specified</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-4">
                        <p class="small text-muted mb-1">Status</p>
                        <p class="mb-0">
                            <span class="badge
                                @if($siteProject->status === 'Live') bg-success
                                @elseif($siteProject->status === 'Development') bg-warning text-dark
                                @else bg-secondary
                                @endif
                            ">{{ $siteProject->status }}</span>
                        </p>
                    </div>
                    <div class="col-md-4">
                        <p class="small text-muted mb-1">Go Live Date</p>
                        <p class="mb-0">
                            @if($siteProject->go_live_date)
                                {{ $siteProject->go_live_date->format('M d, Y') }}
                            @else
                                <span class="text-muted">Not set</span>
                            @endif
                        </p>
                    </div>
                </div>

                @if($siteProject->lead)
                <hr>
                <div class="mb-0">
                    <p class="small text-muted mb-1">Project Lead</p>
                    <p class="mb-0">
                        <i class="bi bi-person-badge"></i> {{ $siteProject->lead->name }}
                        <br>
                        <small class="text-muted">{{ $siteProject->lead->email }}</small>
                    </p>
                </div>
                @endif
            </div>
        </div>

        <!-- Tasks Card -->
        <div class="card">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="bi bi-list-task"></i> Related Tasks
                    <span class="badge bg-secondary">{{ $siteProject->tasks->count() }}</span>
                </h6>
                @if(auth()->user()->hasAnyRole(['Implementation', 'Admin', 'Lead']))
                <a href="{{ route('tasks.create') }}?site_project={{ $siteProject->id }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-circle"></i> New Task
                </a>
                @endif
            </div>
            <div class="card-body">
                @if($siteProject->tasks->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Task No</th>
                                <th>Title</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Assigned To</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($siteProject->tasks->take(10) as $task)
                            <tr>
                                <td>
                                    <a href="{{ route('tasks.show', $task) }}">{{ $task->task_no }}</a>
                                </td>
                                <td>{{ Str::limit($task->title, 40) }}</td>
                                <td>
                                    <span class="badge
                                        @if($task->priority === 'Urgent') bg-danger
                                        @elseif($task->priority === 'High') bg-warning text-dark
                                        @elseif($task->priority === 'Medium') bg-info
                                        @else bg-secondary
                                        @endif
                                    ">{{ $task->priority }}</span>
                                </td>
                                <td>
                                    <span class="badge
                                        @if($task->status === 'Done') bg-success
                                        @elseif($task->status === 'In Progress') bg-primary
                                        @elseif($task->status === 'Blocked') bg-danger
                                        @else bg-light text-dark
                                        @endif
                                    ">{{ $task->status }}</span>
                                </td>
                                <td>
                                    @if($task->developer)
                                        {{ $task->developer->name }}
                                    @else
                                        <span class="text-muted">Unassigned</span>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $task->created_at->diffForHumans() }}</small>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($siteProject->tasks->count() > 10)
                <div class="text-center mt-3">
                    <a href="{{ route('tasks.index') }}?site_project={{ $siteProject->id }}" class="btn btn-sm btn-outline-primary">
                        View All {{ $siteProject->tasks->count() }} Tasks
                    </a>
                </div>
                @endif
                @else
                <div class="text-center text-muted py-4">
                    <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                    <p class="mt-2 mb-0">No tasks yet for this site project.</p>
                    @if(auth()->user()->hasAnyRole(['Implementation', 'Admin', 'Lead']))
                    <p class="small">
                        <a href="{{ route('tasks.create') }}?site_project={{ $siteProject->id }}">Create the first task</a>
                    </p>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Info Card -->
        <div class="card mb-3">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="bi bi-info-circle"></i> Details</h6>
            </div>
            <div class="card-body">
                <p class="small mb-2">
                    <strong>Site Code:</strong><br>
                    {{ $siteProject->site->code }}
                </p>
                <p class="small mb-2">
                    <strong>Site Name:</strong><br>
                    {{ $siteProject->site->name }}
                </p>
                <p class="small mb-2">
                    <strong>Project Name:</strong><br>
                    {{ $siteProject->project->name }}
                </p>
                @if($siteProject->version)
                <p class="small mb-2">
                    <strong>Version:</strong><br>
                    v{{ $siteProject->version }}
                </p>
                @endif
                <p class="small mb-2">
                    <strong>Status:</strong><br>
                    {{ $siteProject->status }}
                </p>
                @if($siteProject->go_live_date)
                <p class="small mb-2">
                    <strong>Go Live Date:</strong><br>
                    {{ $siteProject->go_live_date->format('M d, Y') }}
                </p>
                @endif
                @if($siteProject->lead)
                <p class="small mb-2">
                    <strong>Lead:</strong><br>
                    {{ $siteProject->lead->name }}
                </p>
                @endif
                <hr>
                <p class="small mb-0">
                    <strong>Created:</strong><br>
                    {{ $siteProject->created_at->format('M d, Y h:i A') }}
                </p>
            </div>
        </div>

        <!-- Statistics Card -->
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="bi bi-graph-up"></i> Task Statistics</h6>
            </div>
            <div class="card-body">
                @php
                    $totalTasks = $siteProject->tasks->count();
                    $openTasks = $siteProject->tasks->whereNotIn('status', ['Done', 'Cancelled', 'Rejected'])->count();
                    $doneTasks = $siteProject->tasks->where('status', 'Done')->count();
                    $blockedTasks = $siteProject->tasks->where('status', 'Blocked')->count();
                @endphp

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small">Total Tasks</span>
                    <span class="badge bg-primary">{{ $totalTasks }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small">Open Tasks</span>
                    <span class="badge bg-warning text-dark">{{ $openTasks }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small">Completed</span>
                    <span class="badge bg-success">{{ $doneTasks }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="small">Blocked</span>
                    <span class="badge bg-danger">{{ $blockedTasks }}</span>
                </div>

                @if($totalTasks > 0)
                <hr>
                <div class="progress" style="height: 20px;">
                    <div class="progress-bar bg-success" role="progressbar"
                         style="width: {{ ($doneTasks / $totalTasks) * 100 }}%">
                        {{ round(($doneTasks / $totalTasks) * 100) }}%
                    </div>
                </div>
                <p class="text-center small text-muted mt-2 mb-0">Completion Rate</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
