@extends('layouts.admin')

@section('title', 'Kanban Board')
@section('page-title', 'Kanban Board')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Task Board</h4>
    <div class="d-flex gap-2">
        @if(auth()->user()->hasAnyRole(['Implementation', 'Admin', 'Lead']))
        <a href="{{ route('tasks.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> New Task
        </a>
        @endif
        <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-list-ul"></i> List View
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('tasks.kanban') }}" id="filterForm">
            <div class="row g-3 align-items-end">
                <!-- Site Filter -->
                <div class="col-md-3">
                    <label class="form-label small text-muted">Site</label>
                    <select name="site" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Sites</option>
                        @foreach($sites as $site)
                        <option value="{{ $site->id }}" {{ request('site') == $site->id ? 'selected' : '' }}>
                            {{ $site->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Assigned To Filter -->
                <div class="col-md-3">
                    <label class="form-label small text-muted">Assigned To</label>
                    <select name="assigned" class="form-select form-select-sm" id="assignedFilter" onchange="this.form.submit()">
                        <option value="">All Tasks</option>
                        <option value="me" {{ request('assigned') === 'me' ? 'selected' : '' }}>
                            <i class="bi bi-person-circle"></i> My Tasks
                        </option>
                        <option value="unassigned" {{ request('assigned') === 'unassigned' ? 'selected' : '' }}>
                            Unassigned
                        </option>
                        <option disabled>──────────</option>
                        @foreach($developers as $developer)
                        <option value="{{ $developer->id }}" {{ request('assigned') == $developer->id ? 'selected' : '' }}>
                            {{ $developer->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Quick Filters -->
                <div class="col-md-6">
                    <label class="form-label small text-muted">Quick Filters</label>
                    <div class="d-flex gap-2">
                        <a href="{{ route('tasks.kanban', ['assigned' => 'me'] + request()->except('assigned')) }}"
                           class="btn btn-sm {{ request('assigned') === 'me' ? 'btn-primary' : 'btn-outline-primary' }}">
                            <i class="bi bi-person-circle"></i> My Tasks
                        </a>
                        <a href="{{ route('tasks.kanban', ['assigned' => 'unassigned'] + request()->except('assigned')) }}"
                           class="btn btn-sm {{ request('assigned') === 'unassigned' ? 'btn-warning' : 'btn-outline-warning' }}">
                            <i class="bi bi-inbox"></i> Unassigned
                        </a>
                        @if(request()->hasAny(['site', 'assigned']))
                        <a href="{{ route('tasks.kanban') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Clear Filters
                        </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Active Filters Display -->
            @if(request()->hasAny(['site', 'assigned']))
            <div class="mt-3">
                <small class="text-muted">Active filters:</small>
                <div class="d-flex gap-2 flex-wrap mt-1">
                    @if(request('site'))
                        @php
                            $selectedSite = $sites->firstWhere('id', request('site'));
                        @endphp
                        @if($selectedSite)
                        <span class="badge bg-info">
                            Site: {{ $selectedSite->name }}
                            <a href="{{ route('tasks.kanban', request()->except('site')) }}" class="text-white ms-1">×</a>
                        </span>
                        @endif
                    @endif

                    @if(request('assigned'))
                        <span class="badge bg-primary">
                            Assigned:
                            @if(request('assigned') === 'me')
                                My Tasks
                            @elseif(request('assigned') === 'unassigned')
                                Unassigned
                            @else
                                @php
                                    $selectedDev = $developers->firstWhere('id', request('assigned'));
                                @endphp
                                {{ $selectedDev ? $selectedDev->name : 'Unknown' }}
                            @endif
                            <a href="{{ route('tasks.kanban', request()->except('assigned')) }}" class="text-white ms-1">×</a>
                        </span>
                    @endif
                </div>
            </div>
            @endif
        </form>
    </div>
</div>

<!-- Kanban Board -->
<div class="kanban-board">
    @foreach($columns as $columnKey => $column)
    <div class="kanban-column">
        <div class="kanban-column-header">
            <h6 class="mb-0">{{ $column['title'] }}</h6>
            <span class="badge bg-secondary">{{ $column['tasks']->count() }}</span>
        </div>

        <div class="kanban-column-body" id="column-{{ $columnKey }}" data-column="{{ $columnKey }}" data-statuses="{{ json_encode($column['statuses']) }}">
            @forelse($column['tasks'] as $task)
            <div class="kanban-card" data-task-id="{{ $task->id }}" data-status="{{ $task->status }}">
                <div class="kanban-card-header">
                    <a href="{{ route('tasks.show', $task) }}" class="text-decoration-none text-dark">
                        <strong>{{ $task->task_no }}</strong>
                    </a>
                    <span class="badge
                        @if($task->priority === 'Urgent') bg-danger
                        @elseif($task->priority === 'High') bg-warning text-dark
                        @elseif($task->priority === 'Medium') bg-info
                        @else bg-secondary
                        @endif
                    ">{{ $task->priority }}</span>
                </div>

                <div class="kanban-card-body">
                    <p class="mb-2">{{ Str::limit($task->title, 60) }}</p>

                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-light text-dark">
                            <i class="bi bi-building"></i> {{ $task->siteProject->site->code ?? 'N/A' }}
                        </span>
                        <span class="badge
                            @if($task->type === 'Bug') bg-danger
                            @elseif($task->type === 'New Feature') bg-success
                            @elseif($task->type === 'Enhancement') bg-info
                            @else bg-secondary
                            @endif
                        ">{{ $task->type }}</span>
                    </div>

                    <div class="kanban-card-footer">
                        @if($task->developer)
                        <div class="d-flex align-items-center gap-1">
                            <i class="bi bi-person-circle"></i>
                            <small>{{ Str::limit($task->developer->name, 15) }}</small>
                        </div>
                        @endif

                        @if($task->target_date)
                        <small class="{{ $task->target_date < now() && !in_array($task->status, ['Done', 'Rejected', 'Cancelled']) ? 'text-danger' : 'text-muted' }}">
                            <i class="bi bi-calendar"></i> {{ $task->target_date->format('M d') }}
                        </small>
                        @endif
                    </div>

                    @if($task->status !== 'Done')
                    <div class="kanban-card-status-badge">
                        <small class="text-muted">{{ $task->status }}</small>
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="kanban-empty">
                <i class="bi bi-inbox text-muted"></i>
                <p class="text-muted small mb-0">No tasks</p>
            </div>
            @endforelse
        </div>
    </div>
    @endforeach
</div>

<style>
    .kanban-board {
        display: flex;
        gap: 1rem;
        overflow-x: auto;
        padding-bottom: 1rem;
        min-height: 600px;
    }

    .kanban-column {
        flex: 0 0 300px;
        background: #f8f9fa;
        border-radius: 8px;
        display: flex;
        flex-direction: column;
        max-height: calc(100vh - 250px);
    }

    .kanban-column-header {
        padding: 1rem;
        background: linear-gradient(135deg, rgba(139, 197, 63, 0.08) 0%, rgba(45, 139, 80, 0.08) 100%);
        border-bottom: 2px solid #8BC53F;
        border-radius: 8px 8px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .kanban-column-header h6 {
        color: #2D8B50;
        font-weight: 600;
    }

    .kanban-column-body {
        padding: 1rem;
        overflow-y: auto;
        flex: 1;
        min-height: 100px;
    }

    .kanban-card {
        background: white;
        border-radius: 6px;
        padding: 0.75rem;
        margin-bottom: 0.75rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        cursor: move;
        transition: all 0.2s;
        border-left: 3px solid #8BC53F;
    }

    .kanban-card:hover {
        box-shadow: 0 4px 12px rgba(139, 197, 63, 0.25);
        transform: translateY(-2px);
    }

    .kanban-card.sortable-ghost {
        opacity: 0.4;
        background: #e9ecef;
    }

    .kanban-card.sortable-drag {
        opacity: 1;
        transform: rotate(3deg);
    }

    .kanban-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .kanban-card-body p {
        font-size: 0.9rem;
        line-height: 1.4;
    }

    .kanban-card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 0.5rem;
        padding-top: 0.5rem;
        border-top: 1px solid #e9ecef;
    }

    .kanban-card-status-badge {
        margin-top: 0.5rem;
        font-size: 0.75rem;
    }

    .kanban-empty {
        text-align: center;
        padding: 2rem 1rem;
    }

    .kanban-empty i {
        font-size: 2rem;
        display: block;
        margin-bottom: 0.5rem;
    }

    /* Priority border colors */
    .kanban-card[data-status*="Urgent"] {
        border-left-color: #dc3545;
    }
    .kanban-card[data-status*="High"] {
        border-left-color: #ffc107;
    }
    .kanban-card[data-status*="Medium"] {
        border-left-color: #0dcaf0;
    }
</style>

@push('scripts')
<!-- SortableJS CDN -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>
    // CSRF token for AJAX requests
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Initialize Sortable on each column
    document.querySelectorAll('.kanban-column-body').forEach(column => {
        new Sortable(column, {
            group: 'kanban',
            animation: 150,
            ghostClass: 'sortable-ghost',
            dragClass: 'sortable-drag',
            onEnd: function(evt) {
                const taskCard = evt.item;
                const taskId = taskCard.dataset.taskId;
                const newColumn = evt.to;
                const newStatuses = JSON.parse(newColumn.dataset.statuses);

                // Determine the new status (use first status of the column)
                const newStatus = newStatuses[0];

                // Update task status via AJAX
                fetch(`/tasks/${taskId}/update-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        status: newStatus
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update the card's status badge
                        taskCard.dataset.status = newStatus;
                        const statusBadge = taskCard.querySelector('.kanban-card-status-badge small');
                        if (statusBadge) {
                            statusBadge.textContent = newStatus;
                        }

                        // Show success notification
                        showNotification(data.message, 'success');

                        // Update column counts
                        updateColumnCounts();
                    } else {
                        showNotification('Failed to update task status', 'error');
                        // Revert the move
                        evt.from.insertBefore(taskCard, evt.from.children[evt.oldIndex]);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('An error occurred while updating the task', 'error');
                    // Revert the move
                    evt.from.insertBefore(taskCard, evt.from.children[evt.oldIndex]);
                });
            }
        });
    });

    // Update column counts
    function updateColumnCounts() {
        document.querySelectorAll('.kanban-column').forEach(column => {
            const count = column.querySelectorAll('.kanban-card').length;
            const badge = column.querySelector('.kanban-column-header .badge');
            if (badge) {
                badge.textContent = count;
            }
        });
    }

    // Show notification
    function showNotification(message, type = 'success') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
        alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alertDiv);

        // Auto-remove after 3 seconds
        setTimeout(() => {
            alertDiv.remove();
        }, 3000);
    }
</script>
@endpush
@endsection
