@extends('layouts.admin')

@section('title', 'Create New Task')
@section('page-title', 'Create New Task')

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

        .form-label {
            font-size: 0.9rem;
        }

        .form-select,
        .form-control {
            font-size: 0.9rem;
        }

        .btn {
            width: 100%;
            margin-bottom: 0.5rem;
        }

        .d-flex.gap-2 {
            flex-direction: column;
            gap: 0.5rem !important;
        }

        .row .col-md-6 {
            margin-bottom: 1rem;
        }

        h6 {
            font-size: 1rem;
        }
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('tasks.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

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
                                <option value="{{ $siteProject->id }}" {{ old('site_project_id') == $siteProject->id ? 'selected' : '' }}>
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
                        <small class="text-muted">Select which site and project this task is for</small>
                    </div>

                    <div class="mb-3">
                        <label for="source" class="form-label">Source <span class="text-danger">*</span></label>
                        <select name="source" id="source"
                                class="form-select @error('source') is-invalid @enderror" required>
                            <option value="">Select source...</option>
                            @foreach($sources as $source)
                            <option value="{{ $source }}" {{ old('source') === $source ? 'selected' : '' }}>
                                {{ $source }}
                            </option>
                            @endforeach
                        </select>
                        @error('source')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">
                            <strong>Implementation Request:</strong> Submitted by client/implementation team (requires approval)<br>
                            <strong>Internal Development:</strong> Internal bug fixes or enhancements (auto-approved)
                        </small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                            <select name="type" id="type"
                                    class="form-select @error('type') is-invalid @enderror" required>
                                <option value="">Select type...</option>
                                @foreach($types as $type)
                                <option value="{{ $type }}" {{ old('type') === $type ? 'selected' : '' }}>
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
                                <option value="{{ $priority }}" {{ old('priority', 'Medium') === $priority ? 'selected' : '' }}>
                                    {{ $priority }}
                                </option>
                                @endforeach
                            </select>
                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Affects SLA timeline</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror"
                               id="title" name="title" value="{{ old('title') }}"
                               placeholder="Brief summary of the task" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="6"
                                  placeholder="Detailed description of the task, requirements, expected outcome, etc.">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Provide as much detail as possible to help the development team</small>
                    </div>

                    <hr class="my-4">

                    <!-- Assignment Section (Optional) -->
                    @if(auth()->user()->hasAnyRole(['Lead', 'Admin']))
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h6 class="mb-3">
                                <i class="bi bi-person-plus"></i> Assignment (Optional)
                                <small class="text-muted">- Skip to assign later</small>
                            </h6>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="assigned_to" class="form-label">Assign Developer</label>
                                    <select name="assigned_to" id="assigned_to"
                                            class="form-select form-select-sm @error('assigned_to') is-invalid @enderror">
                                        <option value="">Select developer...</option>
                                        @foreach($developers as $developer)
                                        <option value="{{ $developer->id }}" {{ old('assigned_to') == $developer->id ? 'selected' : '' }}>
                                            {{ $developer->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('assigned_to')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="qa_assigned_to" class="form-label">Assign QA</label>
                                    <select name="qa_assigned_to" id="qa_assigned_to"
                                            class="form-select form-select-sm @error('qa_assigned_to') is-invalid @enderror">
                                        <option value="">Select QA...</option>
                                        @foreach($qaUsers as $qa)
                                        <option value="{{ $qa->id }}" {{ old('qa_assigned_to') == $qa->id ? 'selected' : '' }}>
                                            {{ $qa->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('qa_assigned_to')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-0">
                                <label for="target_date" class="form-label">Target Completion Date</label>
                                <input type="date" class="form-control form-control-sm @error('target_date') is-invalid @enderror"
                                       id="target_date" name="target_date"
                                       value="{{ old('target_date', now()->addDays(5)->format('Y-m-d')) }}"
                                       min="{{ now()->format('Y-m-d') }}">
                                @error('target_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Only applies if both developer and QA are selected</small>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- File Attachments -->
                    <div class="mb-4">
                        <label class="form-label">
                            <i class="bi bi-paperclip"></i> Attachments
                        </label>
                        <input type="file" class="form-control @error('attachments.*') is-invalid @enderror"
                               id="attachments" name="attachments[]" multiple
                               accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg,.gif,.txt,.zip">
                        @error('attachments.*')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">
                            You can upload multiple files. Supported: PDF, Word, Excel, Images, Text, ZIP (Max 10MB each)
                        </small>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Create Task
                        </button>
                        <a href="{{ route('tasks.index') }}" class="btn btn-secondary">
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
                <h6 class="mb-0"><i class="bi bi-info-circle"></i> Task Creation Guide</h6>
            </div>
            <div class="card-body">
                <h6 class="small"><strong>What happens next?</strong></h6>

                <div class="mb-3">
                    <p class="small mb-2"><strong>Implementation Request:</strong></p>
                    <ol class="small mb-0">
                        <li>Task will be in "New" status</li>
                        <li>Lead reviews and approves/rejects</li>
                        <li>Lead assigns developer & QA</li>
                        <li>Development begins</li>
                    </ol>
                </div>

                <div class="mb-3">
                    <p class="small mb-2"><strong>Internal Development:</strong></p>
                    <ol class="small mb-0">
                        <li>Task auto-approved</li>
                        <li>Can assign immediately (optional)</li>
                        <li>Development begins</li>
                    </ol>
                </div>

                @if(auth()->user()->hasAnyRole(['Lead', 'Admin']))
                <hr>
                <div class="alert alert-info small mb-0">
                    <i class="bi bi-lightbulb"></i> <strong>New!</strong> You can now assign developer and QA directly when creating the task to save time.
                </div>
                @endif

                <hr>

                <h6 class="small"><strong>Priority SLA Guidelines:</strong></h6>
                <ul class="small mb-0">
                    <li><strong>Urgent:</strong> Same day</li>
                    <li><strong>High:</strong> 3 business days</li>
                    <li><strong>Medium:</strong> 5 business days</li>
                    <li><strong>Low:</strong> 10 business days</li>
                </ul>
            </div>
        </div>

        @if($siteProjects->isEmpty())
        <div class="card mt-3 border-warning">
            <div class="card-body">
                <div class="d-flex gap-2 align-items-start">
                    <i class="bi bi-exclamation-triangle text-warning" style="font-size: 1.5rem;"></i>
                    <div>
                        <h6 class="mb-1">No Projects Available</h6>
                        <p class="small mb-2">You need to set up site projects before creating tasks.</p>
                        @if(auth()->user()->hasRole('Admin'))
                        <a href="{{ route('admin.site-projects.create') }}" class="btn btn-sm btn-warning">
                            Set up Site Projects
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
