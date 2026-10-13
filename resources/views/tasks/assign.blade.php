@extends('layouts.admin')

@section('title', 'Assign Task: ' . $task->task_no)
@section('page-title', 'Assign Task: ' . $task->task_no)

@section('content')
<div class="mb-4">
    <a href="{{ route('tasks.show', $task) }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Task
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Assign Developer & QA</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('tasks.assign.store', $task) }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <h6>Task Details</h6>
                        <p class="mb-1"><strong>{{ $task->title }}</strong></p>
                        <p class="mb-1 text-muted">
                            <small>
                                {{ $task->siteProject->site->name }} - {{ $task->siteProject->project->name }}
                            </small>
                        </p>
                        <div class="mt-2">
                            <span class="badge bg-secondary">{{ $task->type }}</span>
                            <span class="badge
                                @if($task->priority === 'Urgent') bg-danger
                                @elseif($task->priority === 'High') bg-warning text-dark
                                @elseif($task->priority === 'Medium') bg-info
                                @else bg-secondary
                                @endif
                            ">{{ $task->priority }}</span>
                        </div>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <label for="assigned_to" class="form-label">Assign Developer <span class="text-danger">*</span></label>
                        <select name="assigned_to" id="assigned_to"
                                class="form-select @error('assigned_to') is-invalid @enderror" required>
                            <option value="">Select developer...</option>
                            @forelse($developers as $developer)
                            <option value="{{ $developer->id }}" {{ old('assigned_to', $task->assigned_to) == $developer->id ? 'selected' : '' }}>
                                {{ $developer->name }}
                            </option>
                            @empty
                            <option value="" disabled>No developers available</option>
                            @endforelse
                        </select>
                        @error('assigned_to')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Select the developer who will work on this task</small>
                    </div>

                    <div class="mb-3">
                        <label for="qa_assigned_to" class="form-label">Assign QA <span class="text-danger">*</span></label>
                        <select name="qa_assigned_to" id="qa_assigned_to"
                                class="form-select @error('qa_assigned_to') is-invalid @enderror" required>
                            <option value="">Select QA...</option>
                            @forelse($qaUsers as $qa)
                            <option value="{{ $qa->id }}" {{ old('qa_assigned_to', $task->qa_assigned_to) == $qa->id ? 'selected' : '' }}>
                                {{ $qa->name }}
                            </option>
                            @empty
                            <option value="" disabled>No QA users available</option>
                            @endforelse
                        </select>
                        @error('qa_assigned_to')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Select the QA who will test this task</small>
                    </div>

                    <div class="mb-3">
                        <label for="target_date" class="form-label">Target Completion Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('target_date') is-invalid @enderror"
                               id="target_date" name="target_date"
                               value="{{ old('target_date', $task->target_date?->format('Y-m-d') ?? now()->addDays(5)->format('Y-m-d')) }}"
                               min="{{ now()->format('Y-m-d') }}" required>
                        @error('target_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">
                            SLA Due Date: <strong>{{ $task->sla_due_date?->format('M d, Y') ?? 'Not set' }}</strong>
                        </small>
                    </div>

                    <hr>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Assign Task
                        </button>
                        <a href="{{ route('tasks.show', $task) }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="bi bi-info-circle"></i> Assignment Guidelines</h6>
            </div>
            <div class="card-body">
                <h6 class="small"><strong>Developer Responsibilities:</strong></h6>
                <ul class="small mb-3">
                    <li>Implement the requested feature/fix</li>
                    <li>Update task status to "In Progress"</li>
                    <li>Mark task as "For QA UAT" when ready</li>
                    <li>Can request target date changes</li>
                </ul>

                <h6 class="small"><strong>QA Responsibilities:</strong></h6>
                <ul class="small mb-3">
                    <li>Test completed work</li>
                    <li>Perform QA UAT testing</li>
                    <li>Approve or return to developer</li>
                    <li>Document test results</li>
                </ul>

                <h6 class="small"><strong>Target Date:</strong></h6>
                <p class="small mb-0">
                    Set a realistic completion date based on task complexity and priority. The developer can request changes if needed.
                </p>
            </div>
        </div>

        @if($developers->isEmpty() || $qaUsers->isEmpty())
        <div class="card mt-3 border-warning">
            <div class="card-body">
                <div class="d-flex gap-2 align-items-start">
                    <i class="bi bi-exclamation-triangle text-warning" style="font-size: 1.5rem;"></i>
                    <div>
                        <h6 class="mb-1">Missing Team Members</h6>
                        @if($developers->isEmpty())
                        <p class="small mb-1">No developers available for this site.</p>
                        @endif
                        @if($qaUsers->isEmpty())
                        <p class="small mb-1">No QA users available for this site.</p>
                        @endif
                        @if(auth()->user()->hasRole('Admin'))
                        <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-warning mt-2">
                            Create Users
                        </a>
                        @else
                        <p class="small mb-0 text-muted">Please contact an administrator.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
