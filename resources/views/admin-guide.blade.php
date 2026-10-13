@extends('layouts.admin')

@section('title', 'Admin Guide')
@section('page-title', 'Administration Guide')

@push('styles')
<style>
    .function-card {
        transition: all 0.3s;
        border-left: 4px solid #8BC53F;
    }

    .function-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.1);
    }

    .badge-role {
        font-size: 0.75rem;
        padding: 0.35rem 0.65rem;
    }

    .feature-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: linear-gradient(135deg, rgba(139, 197, 63, 0.1) 0%, rgba(45, 139, 80, 0.1) 100%);
        color: #8BC53F;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .section-header {
        background: linear-gradient(135deg, rgba(139, 197, 63, 0.08) 0%, rgba(45, 139, 80, 0.08) 100%);
        border-left: 4px solid #8BC53F;
        padding: 1rem 1.5rem;
        margin-bottom: 1.5rem;
        border-radius: 8px;
    }

    .section-header h4 {
        color: #2D8B50;
        margin: 0;
        font-weight: 600;
    }

    .shortcut-key {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 0.25rem 0.5rem;
        font-family: monospace;
        font-size: 0.85rem;
    }

    .route-path {
        background: #f8f9fa;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-family: monospace;
        font-size: 0.85rem;
        color: #6c757d;
    }
</style>
@endpush

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            <strong>Welcome to the Project Tracker Admin Guide!</strong> This page provides a comprehensive overview of all administrative functions and features available in the system.
        </div>
    </div>
</div>

<!-- Quick Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="feature-icon mx-auto mb-2">
                    <i class="bi bi-people"></i>
                </div>
                <h5>{{ \App\Models\User::count() }}</h5>
                <small class="text-muted">Total Users</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="feature-icon mx-auto mb-2">
                    <i class="bi bi-building"></i>
                </div>
                <h5>{{ \App\Models\Site::count() }}</h5>
                <small class="text-muted">Active Sites</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="feature-icon mx-auto mb-2">
                    <i class="bi bi-diagram-3"></i>
                </div>
                <h5>{{ \App\Models\Project::count() }}</h5>
                <small class="text-muted">Projects</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="feature-icon mx-auto mb-2">
                    <i class="bi bi-list-task"></i>
                </div>
                <h5>{{ \App\Models\Task::count() }}</h5>
                <small class="text-muted">Total Tasks</small>
            </div>
        </div>
    </div>
</div>

<!-- TASK MANAGEMENT -->
<div class="section-header">
    <h4><i class="bi bi-kanban me-2"></i>Task Management</h4>
</div>

<div class="row g-3 mb-4">
    <!-- Dashboard -->
    <div class="col-md-6">
        <div class="card function-card">
            <div class="card-body">
                <div class="d-flex align-items-start">
                    <div class="feature-icon me-3">
                        <i class="bi bi-speedometer2"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="mb-2">Dashboard</h5>
                        <p class="text-muted small mb-2">Real-time overview of all tasks, statistics, and team performance.</p>
                        <div class="mb-2">
                            <span class="badge badge-role bg-success">All Roles</span>
                        </div>
                        <div class="mb-2">
                            <strong>Features:</strong>
                            <ul class="small mb-0">
                                <li>Task statistics (Open, Overdue, Due This Week)</li>
                                <li>Tasks by Status & Priority charts</li>
                                <li>Recent Tasks list</li>
                                <li>Developer: My Upcoming Tasks</li>
                                <li>Lead/Admin: Overdue Tasks overview</li>
                            </ul>
                        </div>
                        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-arrow-right"></i> Go to Dashboard
                        </a>
                        <span class="route-path ms-2">/dashboard</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kanban Board -->
    <div class="col-md-6">
        <div class="card function-card">
            <div class="card-body">
                <div class="d-flex align-items-start">
                    <div class="feature-icon me-3">
                        <i class="bi bi-kanban"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="mb-2">Kanban Board</h5>
                        <p class="text-muted small mb-2">Visual task board with drag-and-drop functionality.</p>
                        <div class="mb-2">
                            <span class="badge badge-role bg-success">All Roles</span>
                        </div>
                        <div class="mb-2">
                            <strong>Features:</strong>
                            <ul class="small mb-0">
                                <li>Drag & drop tasks between columns</li>
                                <li>5 columns: Backlog, In Progress, QA Testing, Implementation Testing, Done</li>
                                <li>Filter by Site or Assigned Developer</li>
                                <li>Quick filters: My Tasks, Unassigned</li>
                                <li>Real-time status updates via AJAX</li>
                            </ul>
                        </div>
                        <a href="{{ route('tasks.kanban') }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-arrow-right"></i> Open Kanban
                        </a>
                        <span class="route-path ms-2">/kanban</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Task List -->
    <div class="col-md-6">
        <div class="card function-card">
            <div class="card-body">
                <div class="d-flex align-items-start">
                    <div class="feature-icon me-3">
                        <i class="bi bi-list-ul"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="mb-2">Task List View</h5>
                        <p class="text-muted small mb-2">Comprehensive table view of all tasks with advanced filtering.</p>
                        <div class="mb-2">
                            <span class="badge badge-role bg-success">All Roles</span>
                        </div>
                        <div class="mb-2">
                            <strong>Features:</strong>
                            <ul class="small mb-0">
                                <li>Sortable table with pagination</li>
                                <li>Filter by Status, Priority, Site, Project</li>
                                <li>Quick view task details</li>
                                <li>Edit and View actions</li>
                                <li>Site-based access control</li>
                            </ul>
                        </div>
                        <a href="{{ route('tasks.index') }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-arrow-right"></i> View All Tasks
                        </a>
                        <span class="route-path ms-2">/tasks</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Task -->
    <div class="col-md-6">
        <div class="card function-card">
            <div class="card-body">
                <div class="d-flex align-items-start">
                    <div class="feature-icon me-3">
                        <i class="bi bi-plus-circle"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="mb-2">Create New Task</h5>
                        <p class="text-muted small mb-2">Submit new task requests with detailed information.</p>
                        <div class="mb-2">
                            <span class="badge badge-role bg-warning text-dark">Implementation</span>
                            <span class="badge badge-role bg-primary">Lead</span>
                            <span class="badge badge-role bg-danger">Admin</span>
                        </div>
                        <div class="mb-2">
                            <strong>Features:</strong>
                            <ul class="small mb-0">
                                <li>Select Site & Project</li>
                                <li>Choose Type (Bug, Feature, Enhancement, etc.)</li>
                                <li>Set Priority (Urgent, High, Medium, Low)</li>
                                <li>Assign Developer & QA (optional)</li>
                                <li>Set Target Date</li>
                                <li>Automatic task number generation (REQ-YYYY-####)</li>
                                <li>Automatic SLA calculation</li>
                            </ul>
                        </div>
                        <a href="{{ route('tasks.create') }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-arrow-right"></i> Create Task
                        </a>
                        <span class="route-path ms-2">/tasks/create</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Task Details -->
    <div class="col-md-6">
        <div class="card function-card">
            <div class="card-body">
                <div class="d-flex align-items-start">
                    <div class="feature-icon me-3">
                        <i class="bi bi-eye"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="mb-2">View Task Details</h5>
                        <p class="text-muted small mb-2">Complete task information with comments and actions.</p>
                        <div class="mb-2">
                            <span class="badge badge-role bg-success">All Roles</span>
                        </div>
                        <div class="mb-2">
                            <strong>Features:</strong>
                            <ul class="small mb-0">
                                <li>Full task information (Site, Project, Type, Priority, Status)</li>
                                <li>Task description and details</li>
                                <li>Assignment info (Developer, QA, Dates)</li>
                                <li>Comments section with threaded discussions</li>
                                <li>Add new comments</li>
                                <li>Edit task button</li>
                                <li>Lead/Admin: Approve, Reject, Assign actions</li>
                            </ul>
                        </div>
                        <span class="route-path">/tasks/{task_id}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Task -->
    <div class="col-md-6">
        <div class="card function-card">
            <div class="card-body">
                <div class="d-flex align-items-start">
                    <div class="feature-icon me-3">
                        <i class="bi bi-pencil"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="mb-2">Edit Task</h5>
                        <p class="text-muted small mb-2">Update task information and reassign resources.</p>
                        <div class="mb-2">
                            <span class="badge badge-role bg-info">Developer</span>
                            <span class="badge badge-role bg-secondary">QA</span>
                            <span class="badge badge-role bg-primary">Lead</span>
                            <span class="badge badge-role bg-danger">Admin</span>
                        </div>
                        <div class="mb-2">
                            <strong>Features:</strong>
                            <ul class="small mb-0">
                                <li>Edit all task fields (Site/Project, Type, Priority, Status)</li>
                                <li>Update title and description</li>
                                <li>Reassign Developer & QA</li>
                                <li>Change target date</li>
                                <li>Automatic SLA recalculation on priority change</li>
                            </ul>
                        </div>
                        <span class="route-path">/tasks/{task_id}/edit</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Approve/Reject Task -->
    <div class="col-md-6">
        <div class="card function-card">
            <div class="card-body">
                <div class="d-flex align-items-start">
                    <div class="feature-icon me-3">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="mb-2">Approve/Reject Tasks</h5>
                        <p class="text-muted small mb-2">Review and approve or reject new task requests.</p>
                        <div class="mb-2">
                            <span class="badge badge-role bg-primary">Lead</span>
                            <span class="badge badge-role bg-danger">Admin</span>
                        </div>
                        <div class="mb-2">
                            <strong>Features:</strong>
                            <ul class="small mb-0">
                                <li>Approve button: Changes status from "New" to "Approved"</li>
                                <li>Reject button: Changes status to "Rejected"</li>
                                <li>Only available for tasks with "New" status</li>
                                <li>Confirmation prompts to prevent accidental actions</li>
                            </ul>
                        </div>
                        <span class="route-path">/tasks/{task_id}/approve or /reject</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assign Task -->
    <div class="col-md-6">
        <div class="card function-card">
            <div class="card-body">
                <div class="d-flex align-items-start">
                    <div class="feature-icon me-3">
                        <i class="bi bi-person-plus"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="mb-2">Assign Developer & QA</h5>
                        <p class="text-muted small mb-2">Assign tasks to developers and QA testers.</p>
                        <div class="mb-2">
                            <span class="badge badge-role bg-primary">Lead</span>
                            <span class="badge badge-role bg-danger">Admin</span>
                        </div>
                        <div class="mb-2">
                            <strong>Features:</strong>
                            <ul class="small mb-0">
                                <li>Select Developer from active users</li>
                                <li>Select QA from active users</li>
                                <li>Set Target Date</li>
                                <li>Automatically sets Date Assigned</li>
                                <li>Changes task status to "Assigned"</li>
                                <li>Can reassign if already assigned</li>
                            </ul>
                        </div>
                        <span class="route-path">/tasks/{task_id}/assign</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ADMINISTRATION -->
<div class="section-header">
    <h4><i class="bi bi-gear me-2"></i>Administration (Admin Only)</h4>
</div>

<div class="row g-3 mb-4">
    <!-- Sites Management -->
    <div class="col-md-6">
        <div class="card function-card">
            <div class="card-body">
                <div class="d-flex align-items-start">
                    <div class="feature-icon me-3">
                        <i class="bi bi-building"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="mb-2">Sites Management</h5>
                        <p class="text-muted small mb-2">Manage client sites and locations.</p>
                        <div class="mb-2">
                            <span class="badge badge-role bg-danger">Admin Only</span>
                        </div>
                        <div class="mb-2">
                            <strong>CRUD Operations:</strong>
                            <ul class="small mb-0">
                                <li><strong>Create:</strong> Add new sites with name and code</li>
                                <li><strong>Read:</strong> View all sites with user assignments</li>
                                <li><strong>Update:</strong> Edit site details</li>
                                <li><strong>Delete:</strong> Remove sites (if no dependencies)</li>
                            </ul>
                        </div>
                        <div class="mb-2">
                            <strong>Fields:</strong>
                            <ul class="small mb-0">
                                <li>Site Name (e.g., "Main Office")</li>
                                <li>Site Code (e.g., "MO")</li>
                            </ul>
                        </div>
                        <a href="{{ route('admin.sites.index') }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-arrow-right"></i> Manage Sites
                        </a>
                        <span class="route-path ms-2">/admin/sites</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Projects Management -->
    <div class="col-md-6">
        <div class="card function-card">
            <div class="card-body">
                <div class="d-flex align-items-start">
                    <div class="feature-icon me-3">
                        <i class="bi bi-diagram-3"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="mb-2">Projects Management</h5>
                        <p class="text-muted small mb-2">Manage software projects and applications.</p>
                        <div class="mb-2">
                            <span class="badge badge-role bg-danger">Admin Only</span>
                        </div>
                        <div class="mb-2">
                            <strong>CRUD Operations:</strong>
                            <ul class="small mb-0">
                                <li><strong>Create:</strong> Add new projects with name and description</li>
                                <li><strong>Read:</strong> View all projects</li>
                                <li><strong>Update:</strong> Edit project details</li>
                                <li><strong>Delete:</strong> Remove projects (if no dependencies)</li>
                            </ul>
                        </div>
                        <div class="mb-2">
                            <strong>Fields:</strong>
                            <ul class="small mb-0">
                                <li>Project Name (e.g., "CRM System")</li>
                                <li>Description (optional)</li>
                            </ul>
                        </div>
                        <a href="{{ route('admin.projects.index') }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-arrow-right"></i> Manage Projects
                        </a>
                        <span class="route-path ms-2">/admin/projects</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Site Projects -->
    <div class="col-md-6">
        <div class="card function-card">
            <div class="card-body">
                <div class="d-flex align-items-start">
                    <div class="feature-icon me-3">
                        <i class="bi bi-link-45deg"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="mb-2">Site-Project Mapping</h5>
                        <p class="text-muted small mb-2">Link projects to specific sites with version tracking.</p>
                        <div class="mb-2">
                            <span class="badge badge-role bg-danger">Admin Only</span>
                        </div>
                        <div class="mb-2">
                            <strong>CRUD Operations:</strong>
                            <ul class="small mb-0">
                                <li><strong>Create:</strong> Link a project to a site</li>
                                <li><strong>Read:</strong> View all site-project relationships</li>
                                <li><strong>Update:</strong> Edit version numbers</li>
                                <li><strong>Delete:</strong> Remove site-project links</li>
                            </ul>
                        </div>
                        <div class="mb-2">
                            <strong>Features:</strong>
                            <ul class="small mb-0">
                                <li>Associate projects with multiple sites</li>
                                <li>Track version per site (e.g., v1.0, v2.5)</li>
                                <li>Used for task categorization</li>
                            </ul>
                        </div>
                        <a href="{{ route('admin.site-projects.index') }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-arrow-right"></i> Manage Mappings
                        </a>
                        <span class="route-path ms-2">/admin/site-projects</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Management -->
    <div class="col-md-6">
        <div class="card function-card">
            <div class="card-body">
                <div class="d-flex align-items-start">
                    <div class="feature-icon me-3">
                        <i class="bi bi-people"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="mb-2">Users Management</h5>
                        <p class="text-muted small mb-2">Manage system users, roles, and permissions.</p>
                        <div class="mb-2">
                            <span class="badge badge-role bg-danger">Admin Only</span>
                        </div>
                        <div class="mb-2">
                            <strong>CRUD Operations:</strong>
                            <ul class="small mb-0">
                                <li><strong>Create:</strong> Add new users with credentials</li>
                                <li><strong>Read:</strong> View all users with roles</li>
                                <li><strong>Update:</strong> Edit user details, roles, sites</li>
                                <li><strong>Delete:</strong> Remove users</li>
                            </ul>
                        </div>
                        <div class="mb-2">
                            <strong>User Roles:</strong>
                            <ul class="small mb-0">
                                <li><span class="badge bg-danger">Admin</span> - Full system access</li>
                                <li><span class="badge bg-primary">Lead</span> - Approve, assign tasks</li>
                                <li><span class="badge bg-info">Developer</span> - Work on tasks</li>
                                <li><span class="badge bg-secondary">QA</span> - Test tasks</li>
                                <li><span class="badge bg-warning">Implementation</span> - Submit requests</li>
                            </ul>
                        </div>
                        <div class="mb-2">
                            <strong>Features:</strong>
                            <ul class="small mb-0">
                                <li>Assign multiple roles to users</li>
                                <li>Assign users to specific sites (site-based access control)</li>
                                <li>Set password (with show/hide toggle)</li>
                                <li>Active/Inactive status</li>
                            </ul>
                        </div>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-arrow-right"></i> Manage Users
                        </a>
                        <span class="route-path ms-2">/admin/users</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SYSTEM FEATURES -->
<div class="section-header">
    <h4><i class="bi bi-lightning me-2"></i>System Features</h4>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="bi bi-shield-check me-2"></i>Security & Access Control</h6>
            </div>
            <div class="card-body">
                <ul class="mb-0">
                    <li><strong>Role-Based Access Control (RBAC)</strong>
                        <ul class="small">
                            <li>Different permissions per role</li>
                            <li>Multi-role support (users can have multiple roles)</li>
                        </ul>
                    </li>
                    <li><strong>Site-Based Access Control</strong>
                        <ul class="small">
                            <li>Users only see tasks from assigned sites</li>
                            <li>Admin sees all sites</li>
                        </ul>
                    </li>
                    <li><strong>Authentication</strong>
                        <ul class="small">
                            <li>Email & Password login</li>
                            <li>Remember Me functionality</li>
                            <li>Password visibility toggle</li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="bi bi-calendar-event me-2"></i>Task Workflow</h6>
            </div>
            <div class="card-body">
                <ul class="mb-0">
                    <li><strong>Task Statuses:</strong>
                        <ul class="small">
                            <li>New → Approved → Assigned → In Progress</li>
                            <li>For QA UAT → For Implementation UAT</li>
                            <li>Done / Rejected / Cancelled</li>
                            <li>Blocked / For Clarification</li>
                        </ul>
                    </li>
                    <li><strong>Automatic Features:</strong>
                        <ul class="small">
                            <li>Task numbering (REQ-YYYY-####)</li>
                            <li>SLA calculation based on priority</li>
                            <li>Business days calculation (excludes weekends)</li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="bi bi-chat-left-text me-2"></i>Communication</h6>
            </div>
            <div class="card-body">
                <ul class="mb-0">
                    <li><strong>Comments System</strong>
                        <ul class="small">
                            <li>Add comments to tasks</li>
                            <li>Threaded discussions</li>
                            <li>User avatars and roles displayed</li>
                            <li>Timestamp with "time ago" format</li>
                        </ul>
                    </li>
                    <li><strong>Notifications</strong>
                        <ul class="small">
                            <li>Flash messages for actions (success/error)</li>
                            <li>Toast notifications on Kanban updates</li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="bi bi-funnel me-2"></i>Filtering & Search</h6>
            </div>
            <div class="card-body">
                <ul class="mb-0">
                    <li><strong>Task List Filters:</strong>
                        <ul class="small">
                            <li>By Status (New, In Progress, Done, etc.)</li>
                            <li>By Priority (Urgent, High, Medium, Low)</li>
                            <li>By Site</li>
                            <li>By Project</li>
                        </ul>
                    </li>
                    <li><strong>Kanban Filters:</strong>
                        <ul class="small">
                            <li>By Site</li>
                            <li>By Assigned Developer</li>
                            <li>Quick filters: My Tasks, Unassigned</li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- KEYBOARD SHORTCUTS -->
<div class="section-header">
    <h4><i class="bi bi-keyboard me-2"></i>Quick Access URLs</h4>
</div>

<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-muted mb-3">Task Management</h6>
                <table class="table table-sm">
                    <tbody>
                        <tr>
                            <td><span class="route-path">/dashboard</span></td>
                            <td>Dashboard</td>
                        </tr>
                        <tr>
                            <td><span class="route-path">/kanban</span></td>
                            <td>Kanban Board</td>
                        </tr>
                        <tr>
                            <td><span class="route-path">/tasks</span></td>
                            <td>All Tasks</td>
                        </tr>
                        <tr>
                            <td><span class="route-path">/tasks/create</span></td>
                            <td>Create New Task</td>
                        </tr>
                        <tr>
                            <td><span class="route-path">/tasks/{id}</span></td>
                            <td>View Task</td>
                        </tr>
                        <tr>
                            <td><span class="route-path">/tasks/{id}/edit</span></td>
                            <td>Edit Task</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-6">
                <h6 class="text-muted mb-3">Administration</h6>
                <table class="table table-sm">
                    <tbody>
                        <tr>
                            <td><span class="route-path">/admin/sites</span></td>
                            <td>Sites Management</td>
                        </tr>
                        <tr>
                            <td><span class="route-path">/admin/projects</span></td>
                            <td>Projects Management</td>
                        </tr>
                        <tr>
                            <td><span class="route-path">/admin/site-projects</span></td>
                            <td>Site-Project Mapping</td>
                        </tr>
                        <tr>
                            <td><span class="route-path">/admin/users</span></td>
                            <td>Users Management</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- SUPPORT -->
<div class="card border-primary">
    <div class="card-body">
        <h5 class="mb-3"><i class="bi bi-question-circle me-2"></i>Need Help?</h5>
        <p class="mb-2">If you encounter any issues or need assistance:</p>
        <ul class="mb-3">
            <li>Contact your system administrator</li>
            <li>Check the user manual for detailed instructions</li>
            <li>Submit a support ticket for technical issues</li>
        </ul>
        <div class="alert alert-info mb-0">
            <i class="bi bi-info-circle me-2"></i>
            <strong>System Version:</strong> Project Tracker v1.0 |
            <strong>Laravel:</strong> {{ app()->version() }} |
            <strong>PHP:</strong> {{ phpversion() }}
        </div>
    </div>
</div>

@endsection
