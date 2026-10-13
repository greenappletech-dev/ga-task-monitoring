@extends('layouts.admin')

@section('title', 'Site Projects')
@section('page-title', 'Site Projects Management')

@push('styles')
<style>
    @media (max-width: 768px) {
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table {
            font-size: 0.85rem;
            min-width: 800px;
        }

        .table th,
        .table td {
            padding: 0.5rem 0.25rem;
            white-space: nowrap;
        }

        .btn-group {
            flex-direction: row;
        }

        .btn-group .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
        }

        h4 {
            font-size: 1.1rem;
        }
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">All Site Projects</h4>
    <a href="{{ route('admin.site-projects.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Assign Project to Site
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Site</th>
                        <th>Project</th>
                        <th>Project Leads</th>
                        <th>Version</th>
                        <th>Status</th>
                        <th>Go-Live Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($siteProjects as $siteProject)
                    <tr>
                        <td><strong>{{ $siteProject->site->name }}</strong></td>
                        <td><strong>{{ $siteProject->project->name }}</strong></td>
                        <td>
                            @if($siteProject->leads->count() > 0)
                                @foreach($siteProject->leads as $lead)
                                    <span class="badge bg-primary me-1 mb-1">{{ $lead->name }}</span>
                                @endforeach
                            @else
                                <span class="text-muted">Not assigned</span>
                            @endif
                        </td>
                        <td>
                            @if($siteProject->version)
                                <span class="badge bg-secondary">{{ $siteProject->version }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($siteProject->status === 'Live')
                                <span class="badge bg-success">{{ $siteProject->status }}</span>
                            @elseif($siteProject->status === 'Development')
                                <span class="badge bg-warning">{{ $siteProject->status }}</span>
                            @else
                                <span class="badge bg-info">{{ $siteProject->status }}</span>
                            @endif
                        </td>
                        <td>
                            @if($siteProject->go_live_date)
                                {{ $siteProject->go_live_date->format('M d, Y') }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.site-projects.show', $siteProject) }}" class="btn btn-outline-primary" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.site-projects.edit', $siteProject) }}" class="btn btn-outline-secondary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.site-projects.destroy', $siteProject) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to remove this project from this site?');">
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
                            <i class="bi bi-link-45deg" style="font-size: 3rem;"></i>
                            <p class="mt-2">No site projects found. <a href="{{ route('admin.site-projects.create') }}">Assign a project to a site</a>.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($siteProjects->hasPages())
        <div class="mt-3">
            {{ $siteProjects->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
