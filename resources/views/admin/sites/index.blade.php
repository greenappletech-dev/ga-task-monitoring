@extends('layouts.admin')

@section('title', 'Sites')
@section('page-title', 'Sites Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">All Sites</h4>
    <a href="{{ route('admin.sites.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add New Site
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Client / Location</th>
                        <th>Contact</th>
                        <th>Projects</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sites as $site)
                    <tr>
                        <td><strong>{{ $site->code }}</strong></td>
                        <td>{{ $site->name }}</td>
                        <td>
                            @if($site->client || $site->location)
                                {{ $site->client }}@if($site->client && $site->location) · @endif{{ $site->location }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($site->contact_person)
                                {{ $site->contact_person }}
                                @if($site->contact_email)
                                    <br><small class="text-muted">{{ $site->contact_email }}</small>
                                @endif
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-info">{{ $site->site_projects_count }} Projects</span>
                        </td>
                        <td>
                            @if($site->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.sites.show', $site) }}" class="btn btn-outline-primary" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.sites.edit', $site) }}" class="btn btn-outline-secondary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.sites.destroy', $site) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to delete this site?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="bi bi-building" style="font-size: 3rem;"></i>
                            <p class="mt-2">No sites found. <a href="{{ route('admin.sites.create') }}">Create one now</a>.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($sites->hasPages())
        <div class="mt-3">
            {{ $sites->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
