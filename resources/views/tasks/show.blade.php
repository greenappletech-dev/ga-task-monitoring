@extends('layouts.admin')

@section('title', $task->task_no)
@section('page-title', $task->task_no)

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center">
    <a href="{{ route('tasks.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Tasks
    </a>
    <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-primary">
        <i class="bi bi-pencil"></i> Edit Task
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <!-- Task Details Card -->
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ $task->title }}</h5>
                <span class="badge
                    @if($task->status === 'Done') bg-success
                    @elseif($task->status === 'In Progress') bg-primary
                    @elseif($task->status === 'Blocked') bg-danger
                    @elseif($task->status === 'New') bg-secondary
                    @else bg-light text-dark
                    @endif
                ">{{ $task->status }}</span>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="small text-muted mb-1">Site</p>
                        <p class="mb-0">{{ $task->siteProject->site->name ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="small text-muted mb-1">Project</p>
                        <p class="mb-0">{{ $task->siteProject->project->name ?? 'N/A' }}</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <p class="small text-muted mb-1">Type</p>
                        <span class="badge
                            @if($task->type === 'Bug') bg-danger
                            @elseif($task->type === 'New Feature') bg-success
                            @elseif($task->type === 'Enhancement') bg-info
                            @else bg-secondary
                            @endif
                        ">{{ $task->type }}</span>
                    </div>
                    <div class="col-md-3">
                        <p class="small text-muted mb-1">Priority</p>
                        <span class="badge
                            @if($task->priority === 'Urgent') bg-danger
                            @elseif($task->priority === 'High') bg-warning text-dark
                            @elseif($task->priority === 'Medium') bg-info
                            @else bg-secondary
                            @endif
                        ">{{ $task->priority }}</span>
                    </div>
                    <div class="col-md-6">
                        <p class="small text-muted mb-1">Source</p>
                        <p class="mb-0">{{ $task->source }}</p>
                    </div>
                </div>

                <hr>

                <h6>Description</h6>
                @if($task->description)
                <div class="bg-light p-3 rounded">
                    {!! nl2br(e($task->description)) !!}
                </div>
                @else
                <p class="text-muted">No description provided.</p>
                @endif
            </div>
        </div>

        <!-- Comments section -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-chat-left-text"></i> Comments
                    <span class="badge bg-secondary">{{ $task->comments->count() }}</span>
                </h6>
            </div>
            <div class="card-body">
                <!-- Add Comment Form -->
                <form action="{{ route('tasks.comments.store', $task) }}" method="POST" class="mb-4">
                    @csrf
                    <div class="mb-3">
                        <label for="comment" class="form-label">Add a comment</label>
                        <textarea class="form-control @error('comment') is-invalid @enderror"
                                  id="comment" name="comment" rows="3"
                                  placeholder="Write a comment..." required>{{ old('comment') }}</textarea>
                        @error('comment')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-send"></i> Post Comment
                    </button>
                </form>

                <hr>

                <!-- Comments List -->
                <div class="comments-list">
                    @forelse($task->comments as $comment)
                    <div class="comment-item mb-3 pb-3 border-bottom">
                        <div class="d-flex align-items-start gap-3">
                            <div class="comment-avatar">
                                <div class="avatar-circle">
                                    {{ strtoupper(substr($comment->user->name, 0, 2)) }}
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <strong>{{ $comment->user->name }}</strong>
                                        <div class="text-muted small">
                                            @foreach($comment->user->roles ?? [] as $role)
                                                <span class="badge bg-light text-dark">{{ $role }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                    <small class="text-muted">
                                        {{ $comment->created_at->diffForHumans() }}
                                    </small>
                                </div>
                                <div class="comment-text">
                                    {!! nl2br(e($comment->comment)) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-3">
                        <i class="bi bi-chat-dots" style="font-size: 2rem;"></i>
                        <p class="mt-2 mb-0">No comments yet. Be the first to comment!</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Task Info Card -->
        <div class="card mb-3">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="bi bi-info-circle"></i> Task Information</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <p class="small text-muted mb-1">Requested By</p>
                    <p class="mb-0">{{ $task->requester->name ?? 'N/A' }}</p>
                    <small class="text-muted">{{ $task->date_requested->format('M d, Y') }}</small>
                </div>

                <div class="mb-3">
                    <p class="small text-muted mb-1">Assigned To</p>
                    @if($task->developer)
                        <p class="mb-0">{{ $task->developer->name }}</p>
                        @if($task->date_assigned)
                        <small class="text-muted">Assigned on {{ $task->date_assigned->format('M d, Y') }}</small>
                        @endif
                    @else
                        <p class="mb-0 text-muted">Not assigned yet</p>
                    @endif
                </div>

                @if($task->qa)
                <div class="mb-3">
                    <p class="small text-muted mb-1">QA Assigned To</p>
                    <p class="mb-0">{{ $task->qa->name }}</p>
                </div>
                @endif

                <hr>

                <div class="mb-3">
                    <p class="small text-muted mb-1">Target Date</p>
                    @if($task->target_date)
                        <p class="mb-0 {{ $task->target_date < now() && !in_array($task->status, ['Done', 'Rejected', 'Cancelled']) ? 'text-danger fw-bold' : '' }}">
                            {{ $task->target_date->format('M d, Y') }}
                        </p>
                    @else
                        <p class="mb-0 text-muted">Not set</p>
                    @endif
                </div>

                <div class="mb-3">
                    <p class="small text-muted mb-1">SLA Due Date</p>
                    @if($task->sla_due_date)
                        <p class="mb-0">{{ $task->sla_due_date->format('M d, Y') }}</p>
                    @else
                        <p class="mb-0 text-muted">Not set</p>
                    @endif
                </div>

                @if($task->completed_date)
                <div class="mb-3">
                    <p class="small text-muted mb-1">Completed Date</p>
                    <p class="mb-0">{{ $task->completed_date->format('M d, Y') }}</p>
                </div>
                @endif

                <hr>

                <div class="mb-0">
                    <p class="small text-muted mb-1">Created</p>
                    <p class="mb-0 small">{{ $task->created_at->format('M d, Y h:i A') }}</p>
                </div>
            </div>
        </div>

        <!-- Actions Card -->
        @if(auth()->user()->hasRole('Lead') || auth()->user()->hasRole('Admin'))
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="bi bi-gear"></i> Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if($task->status === 'New')
                    <form action="{{ route('tasks.approve', $task) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm w-100"
                                onclick="return confirm('Are you sure you want to approve this task?')">
                            <i class="bi bi-check-circle"></i> Approve Task
                        </button>
                    </form>
                    <form action="{{ route('tasks.reject', $task) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm w-100"
                                onclick="return confirm('Are you sure you want to reject this task?')">
                            <i class="bi bi-x-circle"></i> Reject Task
                        </button>
                    </form>
                    @endif

                    @if(in_array($task->status, ['Approved', 'For Clarification', 'Assigned']))
                    <a href="{{ route('tasks.assign', $task) }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-person-plus"></i>
                        @if($task->status === 'Assigned')
                            Re-assign Developer & QA
                        @else
                            Assign Developer & QA
                        @endif
                    </a>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<style>
    .avatar-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.875rem;
    }

    .comment-item {
        transition: background-color 0.2s;
    }

    .comment-item:hover {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 0.75rem;
        margin-left: -0.75rem;
        margin-right: -0.75rem;
    }

    .comment-text {
        color: #495057;
        line-height: 1.6;
        word-wrap: break-word;
    }

    .comments-list {
        max-height: 600px;
        overflow-y: auto;
    }

    .comments-list::-webkit-scrollbar {
        width: 6px;
    }

    .comments-list::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .comments-list::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }

    .comments-list::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .row {
            margin-left: 0;
            margin-right: 0;
        }

        .col-md-8,
        .col-md-4 {
            padding-left: 0;
            padding-right: 0;
        }

        .card {
            margin-bottom: 1rem;
        }

        .card-body {
            padding: 1rem;
        }

        h5,
        h6 {
            font-size: 1rem;
        }

        .badge {
            font-size: 0.75rem;
        }

        .btn {
            font-size: 0.875rem;
            padding: 0.5rem 1rem;
        }

        .btn-sm {
            font-size: 0.75rem;
            padding: 0.375rem 0.75rem;
        }

        .avatar-circle {
            width: 32px;
            height: 32px;
            font-size: 0.75rem;
        }

        .comment-item {
            padding: 0.5rem;
        }

        .d-grid.gap-2 {
            gap: 0.5rem !important;
        }

        .mb-4,
        .mb-3 {
            margin-bottom: 1rem !important;
        }
    }

    @media (max-width: 576px) {
        .d-flex.justify-content-between {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 0.5rem;
        }

        .d-flex.justify-content-between > * {
            width: 100%;
        }
    }
</style>
@endsection
