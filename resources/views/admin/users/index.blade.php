@extends('layouts.admin')

@section('title', 'Users')
@section('page-title', 'Users Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">All Users</h4>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add New User
    </a>
</div>

<!-- Role Filter -->
<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex gap-2 align-items-center">
            <span class="text-muted">Filter by role:</span>
            <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary {{ !request('role') ? 'active' : '' }}">All</a>
            <a href="{{ route('admin.users.index', ['role' => 'Admin']) }}" class="btn btn-sm btn-outline-secondary {{ request('role') === 'Admin' ? 'active' : '' }}">Admin</a>
            <a href="{{ route('admin.users.index', ['role' => 'Lead']) }}" class="btn btn-sm btn-outline-secondary {{ request('role') === 'Lead' ? 'active' : '' }}">Lead</a>
            <a href="{{ route('admin.users.index', ['role' => 'Developer']) }}" class="btn btn-sm btn-outline-secondary {{ request('role') === 'Developer' ? 'active' : '' }}">Developer</a>
            <a href="{{ route('admin.users.index', ['role' => 'QA']) }}" class="btn btn-sm btn-outline-secondary {{ request('role') === 'QA' ? 'active' : '' }}">QA</a>
            <a href="{{ route('admin.users.index', ['role' => 'Implementation']) }}" class="btn btn-sm btn-outline-secondary {{ request('role') === 'Implementation' ? 'active' : '' }}">Implementation</a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Roles</th>
                        <th>Assigned Sites</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td><strong>{{ $user->name }}</strong></td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @foreach($user->roles ?? [] as $role)
                                <span class="badge bg-primary">{{ $role }}</span>
                            @endforeach
                        </td>
                        <td>
                            @if($user->sites && $user->sites->count() > 0)
                                <span class="badge bg-info">{{ $user->sites->count() }} sites</span>
                            @else
                                <span class="text-muted">All sites</span>
                            @endif
                        </td>
                        <td>
                            @if($user->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-primary" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-secondary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if($user->id !== auth()->id())
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="bi bi-people" style="font-size: 3rem;"></i>
                            <p class="mt-2">No users found. <a href="{{ route('admin.users.create') }}">Create one now</a>.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div class="mt-3">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
