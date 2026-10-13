@extends('layouts.admin')

@section('title', 'Site Details')
@section('page-title', 'Site: ' . $site->name)

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

        .btn {
            width: 100%;
            margin-bottom: 0.5rem;
        }

        .d-flex.gap-2 {
            flex-direction: column;
            gap: 0.5rem !important;
        }
    }
</style>
@endpush

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center">
    <a href="{{ route('admin.sites.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Sites
    </a>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.sites.edit', $site) }}" class="btn btn-sm btn-primary">
            <i class="bi bi-pencil"></i> Edit
        </a>
        <form action="{{ route('admin.sites.destroy', $site) }}" method="POST" class="d-inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-danger"
                    onclick="return confirm('Are you sure you want to delete this site?')">
                <i class="bi bi-trash"></i> Delete
            </button>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <!-- Site Details Card -->
        <div class="card mb-3">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="bi bi-building"></i> Site Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="small text-muted mb-1">Site Name</p>
                        <h4>{{ $site->name }}</h4>
                    </div>
                    <div class="col-md-6">
                        <p class="small text-muted mb-1">Site Code</p>
                        <h4>
                            <span class="badge bg-primary" style="font-size: 1.2rem;">{{ $site->code }}</span>
                        </h4>
                    </div>
                </div>

                <hr>

                <div class="mb-0">
                    <p class="small text-muted mb-1">Status</p>
                    <p class="mb-0">
                        <span class="badge {{ $site->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $site->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Site Projects Card -->
        <div class="card mb-3">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="bi bi-diagram-3"></i> Projects at this Site
                    <span class="badge bg-secondary">{{ $site->siteProjects->count() }}</span>
                </h6>
                <a href="{{ route('admin.site-projects.create') }}?site={{ $site->id }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-circle"></i> Add Project
                </a>
            </div>
            <div class="card-body">
                @if($site->siteProjects->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Project</th>
                                <th>Version</th>
                                <th>Status</th>
                                <th>Lead</th>
                                <th>Tasks</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($site->siteProjects as $siteProject)
                            <tr>
                                <td>
                                    <strong>{{ $siteProject->project->name }}</strong>
                                    @if($siteProject->project->description)
                                    <br>
                                    <small class="text-muted">{{ Str::limit($siteProject->project->description, 50) }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($siteProject->version)
                                        <span class="badge bg-info">v{{ $siteProject->version }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge
                                        @if($siteProject->status === 'Live') bg-success
                                        @elseif($siteProject->status === 'Development') bg-warning text-dark
                                        @else bg-secondary
                                        @endif
                                    ">{{ $siteProject->status }}</span>
                                </td>
                                <td>
                                    @if($siteProject->lead)
                                        {{ $siteProject->lead->name }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $siteProject->tasks->count() }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.site-projects.show', $siteProject) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center text-muted py-4">
                    <i class="bi bi-diagram-3" style="font-size: 3rem;"></i>
                    <p class="mt-2 mb-0">No projects assigned to this site yet.</p>
                    <p class="small">
                        <a href="{{ route('admin.site-projects.create') }}?site={{ $site->id }}">Assign a project</a>
                    </p>
                </div>
                @endif
            </div>
        </div>

        <!-- Assigned Users Card -->
        <div class="card">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="bi bi-people"></i> Assigned Users
                    <span class="badge bg-secondary">{{ $site->users->count() }}</span>
                </h6>
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-person-plus"></i> Manage Users
                </a>
            </div>
            <div class="card-body">
                @if($site->users->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Roles</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($site->users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @foreach($user->roles ?? [] as $role)
                                        <span class="badge bg-light text-dark">{{ $role }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center text-muted py-4">
                    <i class="bi bi-people" style="font-size: 3rem;"></i>
                    <p class="mt-2 mb-0">No users assigned to this site yet.</p>
                    <p class="small">
                        <a href="{{ route('admin.users.index') }}">Assign users to this site</a>
                    </p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Info Card -->
        <div class="card mb-3">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="bi bi-info-circle"></i> Site Details</h6>
            </div>
            <div class="card-body">
                <p class="small mb-2">
                    <strong>Site Name:</strong><br>
                    {{ $site->name }}
                </p>
                <p class="small mb-2">
                    <strong>Site Code:</strong><br>
                    {{ $site->code }}
                </p>
                <p class="small mb-2">
                    <strong>Status:</strong><br>
                    <span class="badge {{ $site->is_active ? 'bg-success' : 'bg-secondary' }}">
                        {{ $site->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </p>
                <hr>
                <p class="small mb-2">
                    <strong>Created:</strong><br>
                    {{ $site->created_at->format('M d, Y h:i A') }}
                </p>
                <p class="small mb-0">
                    <strong>Last Updated:</strong><br>
                    {{ $site->updated_at->format('M d, Y h:i A') }}
                </p>
            </div>
        </div>

        <!-- Statistics Card -->
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="bi bi-graph-up"></i> Statistics</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small">Total Projects</span>
                    <span class="badge bg-primary">{{ $site->siteProjects->count() }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small">Assigned Users</span>
                    <span class="badge bg-success">{{ $site->users->count() }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="small">Total Tasks</span>
                    <span class="badge bg-info">
                        {{ $site->siteProjects->sum(function($sp) { return $sp->tasks->count(); }) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
