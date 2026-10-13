@extends('layouts.admin')

@section('title', 'Edit Site Project')
@section('page-title', 'Edit: ' . $siteProject->site->name . ' - ' . $siteProject->project->name)

@push('styles')
<style>
    @media (max-width: 768px) {
        .col-md-8,
        .col-md-4,
        .col-md-6 {
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

        .row .col-md-6 {
            margin-bottom: 1rem;
        }
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.site-projects.update', $siteProject) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="site_id" class="form-label">Site <span class="text-danger">*</span></label>
                            <select class="form-select @error('site_id') is-invalid @enderror"
                                    id="site_id" name="site_id" required>
                                <option value="">Select a site...</option>
                                @foreach($sites as $site)
                                    <option value="{{ $site->id }}" {{ old('site_id', $siteProject->site_id) == $site->id ? 'selected' : '' }}>
                                        {{ $site->name }} ({{ $site->code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('site_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="project_id" class="form-label">Project <span class="text-danger">*</span></label>
                            <select class="form-select @error('project_id') is-invalid @enderror"
                                    id="project_id" name="project_id" required>
                                <option value="">Select a project...</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ old('project_id', $siteProject->project_id) == $project->id ? 'selected' : '' }}>
                                        {{ $project->name }} ({{ $project->code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('project_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Project Leads</label>
                            <div class="card">
                                <div class="card-body" style="max-height: 200px; overflow-y: auto;">
                                    @if($leads->count() > 0)
                                        @php
                                            $assignedLeadIds = old('lead_ids', $siteProject->leads->pluck('id')->toArray());
                                        @endphp
                                        @foreach($leads as $lead)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                   name="lead_ids[]" value="{{ $lead->id }}"
                                                   id="lead_{{ $lead->id }}"
                                                   {{ in_array($lead->id, $assignedLeadIds) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="lead_{{ $lead->id }}">
                                                {{ $lead->name }}
                                            </label>
                                        </div>
                                        @endforeach
                                    @else
                                        <p class="text-muted small mb-0">No users with Lead role found</p>
                                    @endif
                                </div>
                            </div>
                            <small class="text-muted">Select one or more leads to manage this project at this site</small>
                            @error('lead_ids')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror"
                                    id="status" name="status" required>
                                <option value="Development" {{ old('status', $siteProject->status) === 'Development' ? 'selected' : '' }}>Development</option>
                                <option value="Live" {{ old('status', $siteProject->status) === 'Live' ? 'selected' : '' }}>Live</option>
                                <option value="Maintenance" {{ old('status', $siteProject->status) === 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="version" class="form-label">Version</label>
                            <input type="text" class="form-control @error('version') is-invalid @enderror"
                                   id="version" name="version" value="{{ old('version', $siteProject->version) }}"
                                   placeholder="e.g., 1.0.0">
                            <small class="text-muted">Current version at this site</small>
                            @error('version')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="go_live_date" class="form-label">Go-Live Date</label>
                            <input type="date" class="form-control @error('go_live_date') is-invalid @enderror"
                                   id="go_live_date" name="go_live_date" value="{{ old('go_live_date', $siteProject->go_live_date ? $siteProject->go_live_date->format('Y-m-d') : '') }}">
                            <small class="text-muted">When this project went live</small>
                            @error('go_live_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Update Site Project
                        </button>
                        <a href="{{ route('admin.site-projects.index') }}" class="btn btn-secondary">
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
                <h6 class="mb-0"><i class="bi bi-info-circle"></i> Information</h6>
            </div>
            <div class="card-body">
                <p class="small mb-2"><strong>Created:</strong> {{ $siteProject->created_at->format('M d, Y') }}</p>
                <p class="small mb-0"><strong>Last Updated:</strong> {{ $siteProject->updated_at->format('M d, Y') }}</p>
            </div>
        </div>

        @if($siteProject->tasks && $siteProject->tasks->count() > 0)
        <div class="card mt-3">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="bi bi-list-task"></i> Tasks</h6>
            </div>
            <div class="card-body">
                <p class="small mb-0"><strong>{{ $siteProject->tasks->count() }}</strong> tasks for this site project</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
