@extends('layouts.admin')

@section('title', 'Edit Site')
@section('page-title', 'Edit Site: ' . $site->name)

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.sites.update', $site) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="code" class="form-label">Site Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror"
                                   id="code" name="code" value="{{ old('code', $site->code) }}" required>
                            <small class="text-muted">Unique identifier (e.g., S01, S02)</small>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Site Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name', $site->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="client" class="form-label">Client</label>
                            <input type="text" class="form-control @error('client') is-invalid @enderror"
                                   id="client" name="client" value="{{ old('client', $site->client) }}">
                            @error('client')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" class="form-control @error('location') is-invalid @enderror"
                                   id="location" name="location" value="{{ old('location', $site->location) }}">
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="contact_person" class="form-label">Contact Person</label>
                        <input type="text" class="form-control @error('contact_person') is-invalid @enderror"
                               id="contact_person" name="contact_person" value="{{ old('contact_person', $site->contact_person) }}">
                        @error('contact_person')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="contact_email" class="form-label">Contact Email</label>
                            <input type="email" class="form-control @error('contact_email') is-invalid @enderror"
                                   id="contact_email" name="contact_email" value="{{ old('contact_email', $site->contact_email) }}">
                            @error('contact_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="contact_phone" class="form-label">Contact Phone</label>
                            <input type="text" class="form-control @error('contact_phone') is-invalid @enderror"
                                   id="contact_phone" name="contact_phone" value="{{ old('contact_phone', $site->contact_phone) }}">
                            @error('contact_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                   value="1" {{ old('is_active', $site->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active
                            </label>
                        </div>
                        <small class="text-muted">Inactive sites are hidden from dropdowns</small>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Update Site
                        </button>
                        <a href="{{ route('admin.sites.index') }}" class="btn btn-secondary">
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
                <h6 class="mb-0"><i class="bi bi-info-circle"></i> Site Information</h6>
            </div>
            <div class="card-body">
                <p class="small mb-2"><strong>Created:</strong> {{ $site->created_at->format('M d, Y') }}</p>
                <p class="small mb-0"><strong>Last Updated:</strong> {{ $site->updated_at->format('M d, Y') }}</p>
            </div>
        </div>

        @if($site->siteProjects->count() > 0)
        <div class="card mt-3">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="bi bi-diagram-3"></i> Projects at this Site</h6>
            </div>
            <div class="list-group list-group-flush">
                @foreach($site->siteProjects as $siteProject)
                <div class="list-group-item">
                    <strong>{{ $siteProject->project->name }}</strong>
                    <br><small class="text-muted">{{ $siteProject->status }}</small>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
