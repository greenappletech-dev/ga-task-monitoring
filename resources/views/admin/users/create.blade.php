@extends('layouts.admin')

@section('title', 'Add New User')
@section('page-title', 'Add New User')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                               id="email" name="email" value="{{ old('email') }}" required>
                        <small class="text-muted">Used for login and notifications</small>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Roles <span class="text-danger">*</span></label>
                        <div class="border rounded p-3">
                            @foreach($availableRoles as $role)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="roles[]"
                                       value="{{ $role }}" id="role_{{ $role }}"
                                       {{ in_array($role, old('roles', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="role_{{ $role }}">
                                    <strong>{{ $role }}</strong>
                                    <br><small class="text-muted">
                                        @if($role === 'Admin') Full system access
                                        @elseif($role === 'Lead') Manage projects, assign tasks
                                        @elseif($role === 'Developer') Work on assigned tasks
                                        @elseif($role === 'QA') Test and validate work
                                        @elseif($role === 'Implementation') Submit requests, do UAT
                                        @endif
                                    </small>
                                </label>
                            </div>
                            @endforeach
                        </div>
                        @error('roles')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">A user can have multiple roles</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Assigned Sites</label>
                        <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                            @forelse($sites as $site)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="site_ids[]"
                                       value="{{ $site->id }}" id="site_{{ $site->id }}"
                                       {{ in_array($site->id, old('site_ids', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="site_{{ $site->id }}">
                                    {{ $site->name }} ({{ $site->code }})
                                </label>
                            </div>
                            @empty
                            <p class="text-muted small mb-0">No sites available. <a href="{{ route('admin.sites.create') }}">Create sites first</a>.</p>
                            @endforelse
                        </div>
                        <small class="text-muted">Users see only tasks from their assigned sites (except Admins who see all)</small>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                   value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active
                            </label>
                        </div>
                        <small class="text-muted">Inactive users cannot login</small>
                    </div>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> A temporary password will be generated and displayed after creating the user. Please email it to them.
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Create User
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
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
                <h6 class="mb-0"><i class="bi bi-info-circle"></i> User Roles</h6>
            </div>
            <div class="card-body">
                <p class="small mb-2"><strong>Admin:</strong> Can manage sites, projects, and users. See all tasks.</p>
                <p class="small mb-2"><strong>Lead:</strong> Review requests, assign developers/QA, set target dates.</p>
                <p class="small mb-2"><strong>Developer:</strong> Update task status, request target date changes.</p>
                <p class="small mb-2"><strong>QA:</strong> Test completed work (QA UAT).</p>
                <p class="small mb-0"><strong>Implementation:</strong> Submit requests, perform final UAT.</p>
            </div>
        </div>
    </div>
</div>
@endsection
