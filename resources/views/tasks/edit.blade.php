@extends('layouts.admin')

@section('title', 'Edit Task: ' . $task->task_no)
@section('page-title', 'Edit Task: ' . $task->task_no)

@section('content')
<div class="mb-4">
    <a href="{{ route('tasks.show', $task) }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Task
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('tasks.update', $task) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="site_project_id" class="form-label">Site / Project <span class="text-danger">*</span></label>
                        <select name="site_project_id" id="site_project_id"
                                class="form-select @error('site_project_id') is-invalid @enderror" required>
                            <option value="">Select Site and Project...</option>
                            @php
                                $currentSite = null;
                            @endphp
                            @foreach($siteProjects as $siteProject)
                                @if($currentSite !== $siteProject->site->name)
                                    @if($currentSite !== null)
                                        </optgroup>
                                    @endif
                                    <optgroup label="{{ $siteProject->site->name }}">
                                    @php
                                        $currentSite = $siteProject->site->name;
                                    @endphp
                                @endif
                                <option value="{{ $siteProject->id }}" {{ old('site_project_id', $task->site_project_id) == $siteProject->id ? 'selected' : '' }}>
                                    {{ $siteProject->project->name }} @if($siteProject->version)(v{{ $siteProject->version }})@endif
                                </option>
                            @endforeach
                            @if($currentSite !== null)
                                </optgroup>
                            @endif
                        </select>
                        @error('site_project_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                            <select name="type" id="type"
                                    class="form-select @error('type') is-invalid @enderror" required>
                                <option value="">Select type...</option>
                                @foreach($types as $type)
                                <option value="{{ $type }}" {{ old('type', $task->type) === $type ? 'selected' : '' }}>
                                    {{ $type }}
                                </option>
                                @endforeach
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                            <select name="priority" id="priority"
                                    class="form-select @error('priority') is-invalid @enderror" required>
                                <option value="">Select priority...</option>
                                @foreach($priorities as $priority)
                                <option value="{{ $priority }}" {{ old('priority', $task->priority) === $priority ? 'selected' : '' }}>
                                    {{ $priority }}
                                </option>
                                @endforeach
                            </select>
                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Changing priority will recalculate SLA date</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" id="status"
                                class="form-select @error('status') is-invalid @enderror" required>
                            @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ old('status', $task->status) === $status ? 'selected' : '' }}>
                                {{ $status }}
                            </option>
                            @endforeach
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror"
                               id="title" name="title" value="{{ old('title', $task->title) }}"
                               placeholder="Brief summary of the task" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="6"
                                  placeholder="Detailed description of the task, requirements, expected outcome, etc.">{{ old('description', $task->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr class="my-4">

                    <!-- Assignment Section -->
                    <h6 class="mb-3"><i class="bi bi-person-plus"></i> Assignment</h6>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="assigned_to" class="form-label">Assigned Developer</label>
                            <select name="assigned_to" id="assigned_to"
                                    class="form-select form-select-sm @error('assigned_to') is-invalid @enderror">
                                <option value="">Not assigned</option>
                                @foreach($developers as $developer)
                                <option value="{{ $developer->id }}" {{ old('assigned_to', $task->assigned_to) == $developer->id ? 'selected' : '' }}>
                                    {{ $developer->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('assigned_to')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="qa_assigned_to" class="form-label">Assigned QA</label>
                            <select name="qa_assigned_to" id="qa_assigned_to"
                                    class="form-select form-select-sm @error('qa_assigned_to') is-invalid @enderror">
                                <option value="">Not assigned</option>
                                @foreach($qaUsers as $qa)
                                <option value="{{ $qa->id }}" {{ old('qa_assigned_to', $task->qa_assigned_to) == $qa->id ? 'selected' : '' }}>
                                    {{ $qa->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('qa_assigned_to')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="target_date" class="form-label">Target Date</label>
                            <input type="date" class="form-control form-control-sm @error('target_date') is-invalid @enderror"
                                   id="target_date" name="target_date"
                                   value="{{ old('target_date', $task->target_date ? $task->target_date->format('Y-m-d') : '') }}">
                            @error('target_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Update Task
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
                <h6 class="mb-0"><i class="bi bi-info-circle"></i> Task Information</h6>
            </div>
            <div class="card-body">
                <p class="small mb-2"><strong>Task No:</strong> {{ $task->task_no }}</p>
                <p class="small mb-2"><strong>Source:</strong> {{ $task->source }}</p>
                <p class="small mb-2"><strong>Requested By:</strong> {{ $task->requester->name }}</p>
                <p class="small mb-2"><strong>Date Requested:</strong> {{ $task->date_requested->format('M d, Y') }}</p>
                @if($task->date_assigned)
                <p class="small mb-2"><strong>Date Assigned:</strong> {{ $task->date_assigned->format('M d, Y') }}</p>
                @endif
                <p class="small mb-2"><strong>SLA Due Date:</strong> {{ $task->sla_due_date ? $task->sla_due_date->format('M d, Y') : 'Not set' }}</p>
                <p class="small mb-0"><strong>Created:</strong> {{ $task->created_at->format('M d, Y h:i A') }}</p>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="bi bi-exclamation-circle"></i> Important Notes</h6>
            </div>
            <div class="card-body">
                <ul class="small mb-0">
                    <li>Changing priority will recalculate the SLA due date</li>
                    <li>Task number and source cannot be changed</li>
                    <li>All changes will be visible in task history</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
