@extends('layouts.admin')

@section('title', 'Tasks')
@section('page-title', 'All Tasks')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Task Board</h4>
    @if(auth()->user()->hasAnyRole(['Implementation', 'Admin', 'Lead']))
    <a href="{{ route('tasks.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> New Task
    </a>
    @endif
</div>

<!-- Filters -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('tasks.index') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label small text-muted">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    @foreach($statuses as $status)
                    <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                        {{ $status }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label small text-muted">Priority</label>
                <select name="priority" class="form-select form-select-sm">
                    <option value="">All Priorities</option>
                    @foreach($priorities as $priority)
                    <option value="{{ $priority }}" {{ request('priority') === $priority ? 'selected' : '' }}>
                        {{ $priority }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label small text-muted">Site</label>
                <select name="site" class="form-select form-select-sm">
                    <option value="">All Sites</option>
                    @foreach($sites as $site)
                    <option value="{{ $site->id }}" {{ request('site') == $site->id ? 'selected' : '' }}>
                        {{ $site->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label small text-muted">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary w-100">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                    <a href="{{ route('tasks.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-x"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Task List -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Task No</th>
                        <th>Title</th>
                        <th>Site / Project</th>
                        <th>Type</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Assigned To</th>
                        <th>Target Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tasks as $task)
                    <tr>
                        <td>
                            <a href="{{ route('tasks.show', $task) }}" class="text-decoration-none">
                                <strong>{{ $task->task_no }}</strong>
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('tasks.show', $task) }}" class="text-decoration-none text-dark">
                                {{ Str::limit($task->title, 50) }}
                            </a>
                        </td>
                        <td>
                            <small class="text-muted">
                                {{ $task->siteProject->site->name ?? 'N/A' }}<br>
                                <span class="badge bg-light text-dark">{{ $task->siteProject->project->name ?? 'N/A' }}</span>
                            </small>
                        </td>
                        <td>
                            <span class="badge
                                @if($task->type === 'Bug') bg-danger
                                @elseif($task->type === 'New Feature') bg-success
                                @elseif($task->type === 'Enhancement') bg-info
                                @else bg-secondary
                                @endif
                            ">{{ $task->type }}</span>
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
                            <span class="badge
                                @if($task->status === 'Done') bg-success
                                @elseif($task->status === 'In Progress') bg-primary
                                @elseif($task->status === 'Blocked') bg-danger
                                @elseif($task->status === 'New') bg-secondary
                                @else bg-light text-dark
                                @endif
                            ">{{ $task->status }}</span>
                        </td>
                        <td>
                            @if($task->developer)
                                <small>{{ $task->developer->name }}</small>
                            @else
                                <small class="text-muted">Unassigned</small>
                            @endif
                        </td>
                        <td>
                            @if($task->target_date)
                                <small class="{{ $task->target_date < now() && !in_array($task->status, ['Done', 'Rejected', 'Cancelled']) ? 'text-danger' : '' }}">
                                    {{ $task->target_date->format('M d, Y') }}
                                </small>
                            @else
                                <small class="text-muted">Not set</small>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('tasks.show', $task) }}" class="btn btn-outline-primary" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if(auth()->user()->hasAnyRole(['Developer', 'QA', 'Lead', 'Admin']))
                                <a href="{{ route('tasks.edit', $task) }}" class="btn btn-outline-secondary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-5">
                            <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                            <p class="mt-2 mb-0">No tasks found.</p>
                            @if(auth()->user()->hasAnyRole(['Implementation', 'Admin', 'Lead']))
                            <p class="small">
                                <a href="{{ route('tasks.create') }}">Create your first task</a>
                            </p>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tasks->hasPages())
        <div class="mt-3">
            {{ $tasks->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

<style>
    .table td {
        vertical-align: middle;
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        h4 {
            font-size: 1.1rem;
        }

        .btn {
            font-size: 0.875rem;
            padding: 0.5rem 1rem;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table {
            font-size: 0.85rem;
            min-width: 800px;
        }

        .table th,
        .table td {
            padding: 0.5rem 0.25rem;
            white-space: nowrap;
        }

        .badge {
            font-size: 0.7rem;
        }

        .btn-group {
            flex-direction: row;
        }

        .btn-group .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
        }

        .d-flex.gap-2 {
            flex-wrap: wrap;
            gap: 0.5rem !important;
        }
    }
</style>
@endsection
