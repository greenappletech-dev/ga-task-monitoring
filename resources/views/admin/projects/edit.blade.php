@extends('layouts.admin')

@section('title', 'Edit Project')
@section('page-title', 'Edit Project: ' . $project->name)

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.projects.update', $project) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="code" class="form-label">Project Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror"
                                   id="code" name="code" value="{{ old('code', $project->code) }}" required>
                            <small class="text-muted">Unique identifier (e.g., HRIS, PAYROLL)</small>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Project Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name', $project->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="4">{{ old('description', $project->description) }}</textarea>
                        <small class="text-muted">Brief description of the project/system</small>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                   value="1" {{ old('is_active', $project->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active
                            </label>
                        </div>
                        <small class="text-muted">Inactive projects are hidden from dropdowns</small>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Update Project
                        </button>
                        <a href="{{ route('admin.projects.index') }}" class="btn btn-secondary">
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
                <h6 class="mb-0"><i class="bi bi-info-circle"></i> Project Information</h6>
            </div>
            <div class="card-body">
                <p class="small mb-2"><strong>Created:</strong> {{ $project->created_at->format('M d, Y') }}</p>
                <p class="small mb-0"><strong>Last Updated:</strong> {{ $project->updated_at->format('M d, Y') }}</p>
            </div>
        </div>

        @if($project->siteProjects->count() > 0)
        <div class="card mt-3">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="bi bi-building"></i> Deployed at Sites</h6>
            </div>
            <div class="list-group list-group-flush">
                @foreach($project->siteProjects as $siteProject)
                <div class="list-group-item">
                    <strong>{{ $siteProject->site->name }}</strong>
                    <br><small class="text-muted">{{ $siteProject->status }}
                        @if($siteProject->lead)
                            · Lead: {{ $siteProject->lead->name }}
                        @endif
                    </small>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
