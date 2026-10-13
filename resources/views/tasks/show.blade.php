@extends('layouts.admin')

@section('title', $task->task_no)
@section('page-title', $task->task_no)

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center">
    <a href="{{ route('tasks.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Tasks
    </a>
    @if(auth()->user()->hasAnyRole(['Developer', 'QA', 'Lead', 'Admin']))
    <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-primary">
        <i class="bi bi-pencil"></i> Edit Task
    </a>
    @endif
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
        @if(auth()->user()->hasAnyRole(['Lead', 'Admin', 'QA', 'Implementation']))
        <div class="card mb-3">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="bi bi-gear"></i> Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    {{-- Lead/Admin Actions --}}
                    @if(auth()->user()->hasAnyRole(['Lead', 'Admin']))
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
                    @endif

                    {{-- QA Actions --}}
                    @if(auth()->user()->hasAnyRole(['QA', 'Lead', 'Admin']) && $task->status === 'For QA UAT')
                    <button type="button" class="btn btn-danger btn-sm w-100" data-bs-toggle="modal" data-bs-target="#failQAModal">
                        <i class="bi bi-x-octagon"></i> Fail QA Testing
                    </button>
                    @endif

                    {{-- Implementation Actions --}}
                    @if(auth()->user()->hasAnyRole(['Implementation', 'Lead', 'Admin']) && $task->status === 'For Implementation UAT')
                    <button type="button" class="btn btn-danger btn-sm w-100" data-bs-toggle="modal" data-bs-target="#failImplementationModal">
                        <i class="bi bi-x-octagon"></i> Fail Implementation Testing
                    </button>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- Attachments Card --}}
        <div class="card mb-3">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="bi bi-paperclip"></i> Attachments ({{ $task->attachments->count() }})</h6>
            </div>
            <div class="card-body">
                @if($task->attachments->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($task->attachments as $attachment)
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div class="d-flex align-items-center flex-grow-1">
                                <i class="bi bi-file-earmark-{{
                                    str_contains($attachment->file_type, 'pdf') ? 'pdf' :
                                    (str_contains($attachment->file_type, 'word') || str_contains($attachment->file_type, 'document') ? 'word' :
                                    (str_contains($attachment->file_type, 'excel') || str_contains($attachment->file_type, 'spreadsheet') ? 'excel' :
                                    (str_contains($attachment->file_type, 'image') ? 'image' :
                                    (str_contains($attachment->file_type, 'zip') || str_contains($attachment->file_type, 'compressed') ? 'zip' : 'text'))))
                                }} fs-3 me-3 text-primary"></i>
                                <div class="flex-grow-1">
                                    <p class="mb-0"><strong>{{ $attachment->file_name }}</strong></p>
                                    <small class="text-muted">
                                        {{ number_format($attachment->file_size / 1024, 2) }} KB •
                                        Uploaded by {{ $attachment->uploader->name }} •
                                        {{ $attachment->uploaded_at->diffForHumans() }}
                                    </small>
                                </div>
                            </div>
                            <a href="{{ route('attachments.download', $attachment) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-download"></i> Download
                            </a>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted text-center mb-0 py-3">
                        <i class="bi bi-inbox" style="font-size: 2rem;"></i><br>
                        No attachments yet
                    </p>
                @endif
            </div>
        </div>

        {{-- Movement History Card --}}
        @if($task->history->count() > 0)
        <div class="card mb-3">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="bi bi-clock-history"></i> Task Movement History</h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    @php
                        $previousDate = null;
                    @endphp
                    @foreach($task->history as $index => $historyItem)
                        @php
                            $timeDiff = null;
                            if ($index < $task->history->count() - 1) {
                                $nextItem = $task->history[$index + 1];
                                $timeDiff = $historyItem->changed_at->diffForHumans($nextItem->changed_at, true);
                            }
                        @endphp
                        <div class="timeline-item mb-3">
                            <div class="d-flex">
                                <div class="timeline-marker me-3">
                                    <div class="bg-primary rounded-circle" style="width: 12px; height: 12px; margin-top: 6px;"></div>
                                    @if(!$loop->last)
                                    <div class="bg-secondary" style="width: 2px; height: 100%; margin-left: 5px; margin-top: 4px;"></div>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <div>
                                            <strong class="text-capitalize">{{ str_replace('_', ' ', $historyItem->field) }}</strong>
                                            <span class="text-muted">changed</span>
                                        </div>
                                        <small class="text-muted">{{ $historyItem->changed_at->format('M d, Y h:i A') }}</small>
                                    </div>
                                    <div class="mb-1">
                                        @if($historyItem->old_value)
                                        <span class="badge bg-light text-dark border">{{ $historyItem->old_value }}</span>
                                        <i class="bi bi-arrow-right mx-1"></i>
                                        @endif
                                        <span class="badge bg-primary">{{ $historyItem->new_value }}</span>
                                    </div>
                                    <small class="text-muted">
                                        <i class="bi bi-person"></i> {{ $historyItem->user->name }}
                                        @if($timeDiff)
                                        <span class="ms-2"><i class="bi bi-hourglass-split"></i> Stayed for {{ $timeDiff }}</span>
                                        @endif
                                    </small>
                                    @if($historyItem->reason)
                                    <div class="mt-2">
                                        <small class="text-danger">
                                            <i class="bi bi-info-circle"></i> <strong>Reason:</strong> {{ $historyItem->reason }}
                                        </small>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- Related Tasks Card --}}
        @if($task->parentTask || $task->childTasks->count() > 0)
        <div class="card mb-3">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="bi bi-link-45deg"></i> Related Tasks</h6>
            </div>
            <div class="card-body">
                @if($task->parentTask)
                <div class="mb-3">
                    <p class="small text-muted mb-1"><i class="bi bi-arrow-up-circle"></i> Parent Task</p>
                    <a href="{{ route('tasks.show', $task->parentTask) }}" class="btn btn-sm btn-outline-primary w-100 text-start">
                        <strong>{{ $task->parentTask->task_no }}</strong>
                        <br>
                        <small>{{ Str::limit($task->parentTask->title, 50) }}</small>
                    </a>
                </div>
                @endif

                @if($task->childTasks->count() > 0)
                <div>
                    <p class="small text-muted mb-1"><i class="bi bi-arrow-down-circle"></i> Rework Tasks ({{ $task->childTasks->count() }})</p>
                    @foreach($task->childTasks as $childTask)
                    <a href="{{ route('tasks.show', $childTask) }}" class="btn btn-sm btn-outline-secondary w-100 text-start mb-2">
                        <strong>{{ $childTask->task_no }}</strong>
                        <span class="badge {{ $childTask->status === 'Done' ? 'bg-success' : 'bg-warning' }} float-end">
                            {{ $childTask->status }}
                        </span>
                        <br>
                        <small>{{ Str::limit($childTask->title, 50) }}</small>
                    </a>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Fail QA Modal --}}
<div class="modal fade" id="failQAModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('tasks.fail-qa', $task) }}" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="bi bi-x-octagon"></i> Fail QA Testing</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-danger mb-3">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>This will create a new rework task assigned to the developer.</strong>
                    </p>
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Reason for Failure <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="5" required
                                  placeholder="Please describe the issues found during QA testing..."></textarea>
                        <small class="text-muted">Be specific about what failed and what needs to be fixed.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-octagon"></i> Fail QA & Create Rework Task
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Fail Implementation Modal --}}
<div class="modal fade" id="failImplementationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('tasks.fail-implementation', $task) }}" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="bi bi-x-octagon"></i> Fail Implementation Testing</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-danger mb-3">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>This will create a high-priority rework task assigned to the developer.</strong>
                    </p>
                    <div class="mb-3">
                        <label for="implementation_rejection_reason" class="form-label">Reason for Failure <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="implementation_rejection_reason" name="rejection_reason" rows="5" required
                                  placeholder="Please describe the issues found during implementation testing..."></textarea>
                        <small class="text-muted">Be specific about what failed and what needs to be fixed.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-octagon"></i> Fail Implementation & Create Rework Task
                    </button>
                </div>
            </form>
        </div>
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
