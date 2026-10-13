@extends('layouts.admin')

@section('title', 'Edit User')
@section('page-title', 'Edit User: ' . $user->name)

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                               id="email" name="email" value="{{ old('email', $user->email) }}" required>
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
                                       {{ in_array($role, old('roles', $user->roles ?? [])) ? 'checked' : '' }}>
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
                                       {{ in_array($site->id, old('site_ids', $user->sites->pluck('id')->toArray())) ? 'checked' : '' }}>
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
                                   value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active
                            </label>
                        </div>
                        <small class="text-muted">Inactive users cannot login</small>
                    </div>

                    <hr>

                    <h6>Change Password (Optional)</h6>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                       id="password" name="password" minlength="8">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="bi bi-eye" id="togglePasswordIcon"></i>
                                </button>
                            </div>
                            <small class="text-muted">Leave blank to keep current password</small>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control"
                                       id="password_confirmation" name="password_confirmation" minlength="8">
                                <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirmation">
                                    <i class="bi bi-eye" id="togglePasswordConfirmationIcon"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Update User
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
                <h6 class="mb-0"><i class="bi bi-info-circle"></i> User Information</h6>
            </div>
            <div class="card-body">
                <p class="small mb-2"><strong>Created:</strong> {{ $user->created_at->format('M d, Y') }}</p>
                <p class="small mb-0"><strong>Last Updated:</strong> {{ $user->updated_at->format('M d, Y') }}</p>
            </div>
        </div>

        @if($user->assignedTasks && $user->assignedTasks->count() > 0)
        <div class="card mt-3">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="bi bi-list-task"></i> Task Statistics</h6>
            </div>
            <div class="card-body">
                <p class="small mb-2"><strong>Assigned Tasks:</strong> {{ $user->assignedTasks->count() }}</p>
                <p class="small mb-0"><strong>Requested Tasks:</strong> {{ $user->requestedTasks->count() }}</p>
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const passwordIcon = document.getElementById('togglePasswordIcon');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            passwordIcon.classList.remove('bi-eye');
            passwordIcon.classList.add('bi-eye-slash');
        } else {
            passwordInput.type = 'password';
            passwordIcon.classList.remove('bi-eye-slash');
            passwordIcon.classList.add('bi-eye');
        }
    });

    // Toggle password confirmation visibility
    document.getElementById('togglePasswordConfirmation').addEventListener('click', function() {
        const passwordInput = document.getElementById('password_confirmation');
        const passwordIcon = document.getElementById('togglePasswordConfirmationIcon');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            passwordIcon.classList.remove('bi-eye');
            passwordIcon.classList.add('bi-eye-slash');
        } else {
            passwordInput.type = 'password';
            passwordIcon.classList.remove('bi-eye-slash');
            passwordIcon.classList.add('bi-eye');
        }
    });
</script>
@endpush
@endsection
