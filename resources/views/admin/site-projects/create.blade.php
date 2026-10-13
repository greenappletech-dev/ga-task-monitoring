@extends('layouts.admin')

@section('title', 'Assign Project to Site')
@section('page-title', 'Assign Project to Site')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.site-projects.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="site_id" class="form-label">Site <span class="text-danger">*</span></label>
                            <select class="form-select @error('site_id') is-invalid @enderror"
                                    id="site_id" name="site_id" required>
                                <option value="">Select a site...</option>
                                @foreach($sites as $site)
                                    <option value="{{ $site->id }}" {{ old('site_id') == $site->id ? 'selected' : '' }}>
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
                                    <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
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
                                        @foreach($leads as $lead)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                   name="lead_ids[]" value="{{ $lead->id }}"
                                                   id="lead_{{ $lead->id }}"
                                                   {{ in_array($lead->id, old('lead_ids', [])) ? 'checked' : '' }}>
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
                                <option value="Development" {{ old('status') === 'Development' ? 'selected' : '' }}>Development</option>
                                <option value="Live" {{ old('status') === 'Live' ? 'selected' : '' }}>Live</option>
                                <option value="Maintenance" {{ old('status') === 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="version" class="form-label">Version</label>
                            <input type="text" class="form-control @error('version') is-invalid @enderror"
                                   id="version" name="version" value="{{ old('version') }}"
                                   placeholder="e.g., 1.0.0">
                            <small class="text-muted">Current version at this site</small>
                            @error('version')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="go_live_date" class="form-label">Go-Live Date</label>
                            <input type="date" class="form-control @error('go_live_date') is-invalid @enderror"
                                   id="go_live_date" name="go_live_date" value="{{ old('go_live_date') }}">
                            <small class="text-muted">When this project went live</small>
                            @error('go_live_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Assign Project
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
                <h6 class="mb-0"><i class="bi bi-info-circle"></i> Help</h6>
            </div>
            <div class="card-body">
                <p class="small mb-2"><strong>Site Project:</strong> Connects a project to a specific site.</p>
                <p class="small mb-2"><strong>Example:</strong> HRIS can be deployed at Site 1, Site 2, and Site 3. Each deployment is tracked separately.</p>
                <hr>
                <p class="small mb-2"><strong>Project Lead:</strong> The person responsible for this project at this site.</p>
                <p class="small mb-2"><strong>Status:</strong>
                    <br>• Development - Being built
                    <br>• Live - In production use
                    <br>• Maintenance - Support mode
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
