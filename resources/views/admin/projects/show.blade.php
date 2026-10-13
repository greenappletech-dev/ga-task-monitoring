@extends('layouts.admin')

@section('title', 'Project Details')
@section('page-title', 'Project: ' . $project->name)

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
    <a href="{{ route('admin.projects.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Projects
    </a>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.projects.edit', $project) }}" class="btn btn-sm btn-primary">
            <i class="bi bi-pencil"></i> Edit
        </a>
        <form action="{{ route('admin.projects.destroy', $project) }}" method="POST" class="d-inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-danger"
                    onclick="return confirm('Are you sure you want to delete this project?')">
                <i class="bi bi-trash"></i> Delete
            </button>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <!-- Project Details Card -->
        <div class="card mb-3">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="bi bi-diagram-3"></i> Project Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="small text-muted mb-1">Project Name</p>
                        <h4>{{ $project->name }}</h4>
                    </div>
                    <div class="col-md-6">
                        <p class="small text-muted mb-1">Project Code</p>
                        <h4>
                            <span class="badge bg-primary" style="font-size: 1.2rem;">{{ $project->code }}</span>
                        </h4>
                    </div>
                </div>

                @if($project->description)
                <hr>
                <div class="mb-3">
                    <p class="small text-muted mb-1">Description</p>
                    <p class="mb-0">{{ $project->description }}</p>
                </div>
                @endif

                <hr>

                <div class="mb-0">
                    <p class="small text-muted mb-1">Status</p>
                    <p class="mb-0">
                        <span class="badge {{ $project->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $project->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Sites Using This Project Card -->
        <div class="card mb-3">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="bi bi-building"></i> Sites Using This Project
                    <span class="badge bg-secondary">{{ $project->siteProjects->count() }}</span>
                </h6>
                <a href="{{ route('admin.site-projects.create') }}?project={{ $project->id }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-circle"></i> Assign to Site
                </a>
            </div>
            <div class="card-body">
                @if($project->siteProjects->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Site</th>
                                <th>Version</th>
                                <th>Status</th>
                                <th>Lead</th>
                                <th>Tasks</th>
                                <th>Go-Live Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($project->siteProjects as $siteProject)
                            <tr>
                                <td>
                                    <strong>{{ $siteProject->site->name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $siteProject->site->code }}</small>
                                </td>
                                <td>
                                    @if($siteProject->version)
                                        <span class="badge bg-info">v{{ $siteProject->version }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge
                                        @if($siteProject->status === 'Live') bg-success
                                        @elseif($siteProject->status === 'Development') bg-warning text-dark
                                        @else bg-secondary
                                        @endif
                                    ">{{ $siteProject->status }}</span>
                                </td>
                                <td>
                                    @if($siteProject->lead)
                                        {{ $siteProject->lead->name }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $siteProject->tasks->count() }}</span>
                                </td>
                                <td>
                                    @if($siteProject->go_live_date)
                                        {{ $siteProject->go_live_date->format('M d, Y') }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.site-projects.show', $siteProject) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center text-muted py-4">
                    <i class="bi bi-building" style="font-size: 3rem;"></i>
                    <p class="mt-2 mb-0">This project is not assigned to any site yet.</p>
                    <p class="small">
                        <a href="{{ route('admin.site-projects.create') }}?project={{ $project->id }}">Assign to a site</a>
                    </p>
                </div>
                @endif
            </div>
        </div>

        <!-- All Tasks for This Project -->
        <div class="card">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="bi bi-list-task"></i> All Tasks
                    <span class="badge bg-secondary">
                        {{ $project->siteProjects->sum(function($sp) { return $sp->tasks->count(); }) }}
                    </span>
                </h6>
            </div>
            <div class="card-body">
                @php
                    $allTasks = collect();
                    foreach($project->siteProjects as $sp) {
                        $allTasks = $allTasks->merge($sp->tasks);
                    }
                    $allTasks = $allTasks->sortByDesc('created_at')->take(10);
                @endphp

                @if($allTasks->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Task No</th>
                                <th>Site</th>
                                <th>Title</th>
                                <th>Status</th>
                                <th>Priority</th>
                                <th>Assigned</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($allTasks as $task)
                            <tr>
                                <td>
                                    <a href="{{ route('tasks.show', $task) }}">{{ $task->task_no }}</a>
                                </td>
                                <td>
                                    <small>{{ $task->siteProject->site->code }}</small>
                                </td>
                                <td>{{ Str::limit($task->title, 40) }}</td>
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
                                    <span class="badge
                                        @if($task->priority === 'Urgent') bg-danger
                                        @elseif($task->priority === 'High') bg-warning text-dark
                                        @elseif($task->priority === 'Medium') bg-info
                                        @else bg-secondary
                                        @endif
                                    ">{{ $task->priority }}</span>
                                </td>
                                <td>
                                    @if($task->developer)
                                        <small>{{ $task->developer->name }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center text-muted py-4">
                    <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                    <p class="mt-2 mb-0">No tasks yet for this project.</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Info Card -->
        <div class="card mb-3">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="bi bi-info-circle"></i> Project Details</h6>
            </div>
            <div class="card-body">
                <p class="small mb-2">
                    <strong>Project Name:</strong><br>
                    {{ $project->name }}
                </p>
                <p class="small mb-2">
                    <strong>Project Code:</strong><br>
                    {{ $project->code }}
                </p>
                @if($project->description)
                <p class="small mb-2">
                    <strong>Description:</strong><br>
                    {{ Str::limit($project->description, 100) }}
                </p>
                @endif
                <p class="small mb-2">
                    <strong>Status:</strong><br>
                    <span class="badge {{ $project->is_active ? 'bg-success' : 'bg-secondary' }}">
                        {{ $project->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </p>
                <hr>
                <p class="small mb-2">
                    <strong>Created:</strong><br>
                    {{ $project->created_at->format('M d, Y h:i A') }}
                </p>
                <p class="small mb-0">
                    <strong>Last Updated:</strong><br>
                    {{ $project->updated_at->format('M d, Y h:i A') }}
                </p>
            </div>
        </div>

        <!-- Statistics Card -->
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="bi bi-graph-up"></i> Statistics</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small">Total Sites</span>
                    <span class="badge bg-primary">{{ $project->siteProjects->count() }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small">Live Sites</span>
                    <span class="badge bg-success">
                        {{ $project->siteProjects->where('status', 'Live')->count() }}
                    </span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small">Development</span>
                    <span class="badge bg-warning text-dark">
                        {{ $project->siteProjects->where('status', 'Development')->count() }}
                    </span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="small">Total Tasks</span>
                    <span class="badge bg-info">
                        {{ $project->siteProjects->sum(function($sp) { return $sp->tasks->count(); }) }}
                    </span>
                </div>

                @php
                    $totalTasks = $project->siteProjects->sum(function($sp) { return $sp->tasks->count(); });
                    $doneTasks = 0;
                    foreach($project->siteProjects as $sp) {
                        $doneTasks += $sp->tasks->where('status', 'Done')->count();
                    }
                @endphp

                @if($totalTasks > 0)
                <hr>
                <div class="progress" style="height: 20px;">
                    <div class="progress-bar bg-success" role="progressbar"
                         style="width: {{ ($doneTasks / $totalTasks) * 100 }}%">
                        {{ round(($doneTasks / $totalTasks) * 100) }}%
                    </div>
                </div>
                <p class="text-center small text-muted mt-2 mb-0">Overall Completion Rate</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
