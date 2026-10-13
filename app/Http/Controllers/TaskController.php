<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\TaskHistory;
use App\Models\TaskAttachment;
use App\Models\SiteProject;
use App\Models\Site;
use App\Models\User;
use App\Models\SlaRule;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Start query with relationships
        $query = Task::with(['siteProject.site', 'siteProject.project', 'requester', 'developer', 'qa']);

        // Filter by user's assigned sites (unless Admin)
        if (!$user->hasRole('Admin')) {
            $userSiteIds = $user->sites->pluck('id')->toArray();
            if (!empty($userSiteIds)) {
                $query->whereHas('siteProject', function($q) use ($userSiteIds) {
                    $q->whereIn('site_id', $userSiteIds);
                });
            }
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->has('priority') && $request->priority) {
            $query->where('priority', $request->priority);
        }

        // Filter by site
        if ($request->has('site') && $request->site) {
            $query->whereHas('siteProject', function($q) use ($request) {
                $q->where('site_id', $request->site);
            });
        }

        // Filter by project
        if ($request->has('project') && $request->project) {
            $query->whereHas('siteProject', function($q) use ($request) {
                $q->where('project_id', $request->project);
            });
        }

        // Order by newest first
        $tasks = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get filter options
        $statuses = ['New', 'For Clarification', 'Approved', 'Assigned', 'In Progress', 'Blocked', 'For QA UAT', 'For Implementation UAT', 'Done', 'Rejected', 'Cancelled', 'On Hold'];
        $priorities = ['Urgent', 'High', 'Medium', 'Low'];

        // Get sites for filter (based on user access)
        if ($user->hasRole('Admin')) {
            $sites = Site::active()->orderBy('name')->get();
        } else {
            $sites = $user->sites;
        }

        return view('tasks.index', compact('tasks', 'statuses', 'priorities', 'sites'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = auth()->user();

        // Get site projects filtered by user's assigned sites
        if ($user->hasRole('Admin')) {
            $siteProjects = SiteProject::with(['site', 'project'])
                ->whereHas('site', function($q) {
                    $q->where('is_active', true);
                })
                ->whereHas('project', function($q) {
                    $q->where('is_active', true);
                })
                ->orderBy('site_id')
                ->get();

            // Get all developers and QA for assignment
            $developers = User::whereJsonContains('roles', 'Developer')
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
            $qaUsers = User::whereJsonContains('roles', 'QA')
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        } else {
            $userSiteIds = $user->sites->pluck('id')->toArray();
            $siteProjects = SiteProject::with(['site', 'project'])
                ->whereIn('site_id', $userSiteIds)
                ->whereHas('site', function($q) {
                    $q->where('is_active', true);
                })
                ->whereHas('project', function($q) {
                    $q->where('is_active', true);
                })
                ->orderBy('site_id')
                ->get();

            // Get developers and QA from user's assigned sites
            $developers = User::whereJsonContains('roles', 'Developer')
                ->where('is_active', true)
                ->whereHas('sites', function($q) use ($userSiteIds) {
                    $q->whereIn('sites.id', $userSiteIds);
                })
                ->orderBy('name')
                ->get();
            $qaUsers = User::whereJsonContains('roles', 'QA')
                ->where('is_active', true)
                ->whereHas('sites', function($q) use ($userSiteIds) {
                    $q->whereIn('sites.id', $userSiteIds);
                })
                ->orderBy('name')
                ->get();
        }

        // Available options
        $types = ['New Feature', 'Enhancement', 'Bug', 'Data Fix', 'Support', 'Report'];
        $priorities = ['Urgent', 'High', 'Medium', 'Low'];
        $sources = ['Implementation Request', 'Internal Development'];

        return view('tasks.create', compact('siteProjects', 'types', 'priorities', 'sources', 'developers', 'qaUsers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'site_project_id' => 'required|exists:site_projects,id',
            'type' => 'required|in:New Feature,Enhancement,Bug,Data Fix,Support,Report',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'source' => 'required|in:Implementation Request,Internal Development',
            'priority' => 'required|in:Urgent,High,Medium,Low',
            'assigned_to' => 'nullable|exists:users,id',
            'qa_assigned_to' => 'nullable|exists:users,id',
            'target_date' => 'nullable|date|after_or_equal:today',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240', // 10MB max per file
        ]);

        // Generate task number
        $year = date('Y');
        $lastTask = Task::where('task_no', 'like', "REQ-{$year}-%")
            ->orderBy('task_no', 'desc')
            ->first();

        if ($lastTask) {
            $lastNumber = (int) substr($lastTask->task_no, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        $taskNo = "REQ-{$year}-{$newNumber}";

        // Calculate SLA due date based on priority
        $slaRule = SlaRule::where('priority', $validated['priority'])->first();
        $businessDays = $slaRule ? $slaRule->business_days : 5;

        $dateRequested = Carbon::now();
        $slaDueDate = $this->addBusinessDays($dateRequested, $businessDays);

        // Determine initial status based on source and assignment
        if ($validated['source'] === 'Implementation Request') {
            // Implementation requests start as "New" and need approval
            $status = 'New';
        } else {
            // Internal development is auto-approved
            // If assigned, set to "Assigned", otherwise "Approved"
            $status = ($request->filled('assigned_to') && $request->filled('qa_assigned_to')) ? 'Assigned' : 'Approved';
        }

        // Create task
        $taskData = [
            'task_no' => $taskNo,
            'site_project_id' => $validated['site_project_id'],
            'type' => $validated['type'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'source' => $validated['source'],
            'priority' => $validated['priority'],
            'status' => $status,
            'requested_by' => auth()->id(),
            'date_requested' => $dateRequested,
            'sla_due_date' => $slaDueDate,
        ];

        // Add assignment fields if provided
        if ($request->filled('assigned_to') && $request->filled('qa_assigned_to') && $request->filled('target_date')) {
            $taskData['assigned_to'] = $validated['assigned_to'];
            $taskData['qa_assigned_to'] = $validated['qa_assigned_to'];
            $taskData['target_date'] = $validated['target_date'];
            $taskData['date_assigned'] = Carbon::now();
        }

        $task = Task::create($taskData);

        // Handle file uploads
        $this->handleFileUploads($request, $task);

        $message = "Task {$taskNo} created successfully.";
        if ($status === 'Assigned') {
            $message .= " Task has been assigned to developer and QA.";
        }

        return redirect()->route('tasks.show', $task)
            ->with('success', $message);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $task = Task::with([
            'siteProject.site',
            'siteProject.project',
            'requester',
            'developer',
            'qa',
            'comments.user',
            'history.user',
            'attachments.uploader',
            'parentTask',
            'childTasks',
            'rejectedBy'
        ])->findOrFail($id);

        return view('tasks.show', compact('task'));
    }

    /**
     * Store a comment on a task
     */
    public function storeComment(Request $request, Task $task)
    {
        $validated = $request->validate([
            'comment' => 'required|string|max:5000',
        ]);

        $task->comments()->create([
            'user_id' => auth()->id(),
            'comment' => $validated['comment'],
        ]);

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Comment added successfully.');
    }

    /**
     * Approve a task (Lead/Admin only)
     */
    public function approve(Task $task)
    {
        if ($task->status !== 'New') {
            return redirect()->route('tasks.show', $task)
                ->with('error', 'Only tasks with "New" status can be approved.');
        }

        $task->update([
            'status' => 'Approved',
        ]);

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Task approved successfully. You can now assign a developer and QA.');
    }

    /**
     * Reject a task (Lead/Admin only)
     */
    public function reject(Request $request, Task $task)
    {
        if ($task->status !== 'New') {
            return redirect()->route('tasks.show', $task)
                ->with('error', 'Only tasks with "New" status can be rejected.');
        }

        $task->update([
            'status' => 'Rejected',
        ]);

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Task rejected.');
    }

    /**
     * Fail QA Testing - Creates a new rework task
     */
    public function failQA(Request $request, Task $task)
    {
        $user = auth()->user();

        // Only QA, Lead, and Admin can fail QA testing
        if (!$user->hasAnyRole(['QA', 'Lead', 'Admin'])) {
            return redirect()->route('tasks.show', $task)
                ->with('error', 'You do not have permission to fail QA testing.');
        }

        if ($task->status !== 'For QA UAT') {
            return redirect()->route('tasks.show', $task)
                ->with('error', 'Only tasks in QA Testing can be failed.');
        }

        // Check if task has developer assigned
        if (!$task->assigned_to) {
            return redirect()->route('tasks.show', $task)
                ->with('error', 'Cannot fail QA testing: Task has no developer assigned. Please assign a developer first.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        // Log status change
        $this->logTaskHistory($task, 'status', $task->status, 'Blocked', 'QA Testing Failed: ' . $validated['rejection_reason']);

        // Update original task
        $task->update([
            'status' => 'Blocked',
            'rejection_reason' => $validated['rejection_reason'],
            'rejected_by' => $user->id,
            'rejected_at' => now(),
            'return_count' => ($task->return_count ?? 0) + 1,
        ]);

        // Create new rework task
        $newTask = Task::create([
            'task_no' => 'TASK-' . strtoupper(uniqid()),
            'parent_task_id' => $task->id,
            'site_project_id' => $task->site_project_id,
            'type' => 'Bug',
            'title' => '[REWORK] ' . $task->title,
            'description' => "**Original Task:** {$task->task_no}\n\n**QA Rejection Reason:**\n{$validated['rejection_reason']}\n\n---\n\n**Original Description:**\n{$task->description}",
            'source' => 'QA Testing',
            'priority' => $task->priority,
            'status' => 'Assigned',
            'requested_by' => $user->id,
            'date_requested' => now(),
            'assigned_to' => $task->assigned_to, // Assign back to same developer
            'qa_assigned_to' => $task->qa_assigned_to,
            'date_assigned' => now(),
            'target_date' => now()->addDays(2), // 2-day turnaround for rework
            'sla_due_date' => now()->addDays(3),
        ]);

        return redirect()->route('tasks.show', $newTask)
            ->with('success', "QA Test Failed. New rework task {$newTask->task_no} has been created and assigned to the developer.");
    }

    /**
     * Fail Implementation Testing - Creates a new rework task
     */
    public function failImplementation(Request $request, Task $task)
    {
        $user = auth()->user();

        // Only Implementation, Lead, and Admin can fail implementation testing
        if (!$user->hasAnyRole(['Implementation', 'Lead', 'Admin'])) {
            return redirect()->route('tasks.show', $task)
                ->with('error', 'You do not have permission to fail implementation testing.');
        }

        if ($task->status !== 'For Implementation UAT') {
            return redirect()->route('tasks.show', $task)
                ->with('error', 'Only tasks in Implementation Testing can be failed.');
        }

        // Check if task has developer assigned
        if (!$task->assigned_to) {
            return redirect()->route('tasks.show', $task)
                ->with('error', 'Cannot fail Implementation testing: Task has no developer assigned. Please assign a developer first.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        // Log status change
        $this->logTaskHistory($task, 'status', $task->status, 'Blocked', 'Implementation Testing Failed: ' . $validated['rejection_reason']);

        // Update original task
        $task->update([
            'status' => 'Blocked',
            'rejection_reason' => $validated['rejection_reason'],
            'rejected_by' => $user->id,
            'rejected_at' => now(),
            'return_count' => ($task->return_count ?? 0) + 1,
        ]);

        // Create new rework task
        $newTask = Task::create([
            'task_no' => 'TASK-' . strtoupper(uniqid()),
            'parent_task_id' => $task->id,
            'site_project_id' => $task->site_project_id,
            'type' => 'Bug',
            'title' => '[REWORK] ' . $task->title,
            'description' => "**Original Task:** {$task->task_no}\n\n**Implementation Rejection Reason:**\n{$validated['rejection_reason']}\n\n---\n\n**Original Description:**\n{$task->description}",
            'source' => 'Implementation Testing',
            'priority' => 'High', // Bump priority for implementation failures
            'status' => 'Assigned',
            'requested_by' => $user->id,
            'date_requested' => now(),
            'assigned_to' => $task->assigned_to, // Assign back to same developer
            'qa_assigned_to' => $task->qa_assigned_to,
            'date_assigned' => now(),
            'target_date' => now()->addDays(1), // 1-day turnaround for implementation failures
            'sla_due_date' => now()->addDays(2),
        ]);

        return redirect()->route('tasks.show', $newTask)
            ->with('success', "Implementation Test Failed. New rework task {$newTask->task_no} has been created and assigned to the developer.");
    }

    /**
     * Show assign form (Lead/Admin only)
     */
    public function assignForm(Task $task)
    {
        if (!in_array($task->status, ['Approved', 'For Clarification', 'Assigned'])) {
            return redirect()->route('tasks.show', $task)
                ->with('error', 'This task cannot be assigned in its current status.');
        }

        $user = auth()->user();

        // Get developers and QA from the same site as the task
        $siteId = $task->siteProject->site_id;

        if ($user->hasRole('Admin')) {
            $developers = User::whereJsonContains('roles', 'Developer')
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
            $qaUsers = User::whereJsonContains('roles', 'QA')
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        } else {
            $developers = User::whereJsonContains('roles', 'Developer')
                ->where('is_active', true)
                ->whereHas('sites', function($q) use ($siteId) {
                    $q->where('sites.id', $siteId);
                })
                ->orderBy('name')
                ->get();
            $qaUsers = User::whereJsonContains('roles', 'QA')
                ->where('is_active', true)
                ->whereHas('sites', function($q) use ($siteId) {
                    $q->where('sites.id', $siteId);
                })
                ->orderBy('name')
                ->get();
        }

        return view('tasks.assign', compact('task', 'developers', 'qaUsers'));
    }

    /**
     * Store task assignment (Lead/Admin only)
     */
    public function assignStore(Request $request, Task $task)
    {
        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
            'qa_assigned_to' => 'required|exists:users,id',
            'target_date' => 'required|date|after_or_equal:today',
        ]);

        $task->update([
            'assigned_to' => $validated['assigned_to'],
            'qa_assigned_to' => $validated['qa_assigned_to'],
            'date_assigned' => Carbon::now(),
            'target_date' => $validated['target_date'],
            'status' => 'Assigned',
        ]);

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Task assigned successfully to developer and QA.');
    }

    /**
     * Display Kanban board
     */
    public function kanban(Request $request)
    {
        $user = auth()->user();

        // Start query with relationships
        $query = Task::with(['siteProject.site', 'siteProject.project', 'requester', 'developer', 'qa']);

        // Filter by user's assigned sites (unless Admin)
        if (!$user->hasRole('Admin')) {
            $userSiteIds = $user->sites->pluck('id')->toArray();
            if (!empty($userSiteIds)) {
                $query->whereHas('siteProject', function($q) use ($userSiteIds) {
                    $q->whereIn('site_id', $userSiteIds);
                });
            }
        }

        // Filter by site if requested
        if ($request->has('site') && $request->site) {
            $query->whereHas('siteProject', function($q) use ($request) {
                $q->where('site_id', $request->site);
            });
        }

        // Filter by assigned user
        if ($request->has('assigned') && $request->assigned) {
            if ($request->assigned === 'me') {
                // Filter to current user's tasks
                $query->where('assigned_to', $user->id);
            } elseif ($request->assigned === 'unassigned') {
                // Filter to unassigned tasks
                $query->whereNull('assigned_to');
            } else {
                // Filter to specific user
                $query->where('assigned_to', $request->assigned);
            }
        }

        // Get all tasks
        $allTasks = $query->orderBy('created_at', 'desc')->get();

        // Group tasks by Kanban columns
        $columns = [
            'backlog' => [
                'title' => 'Backlog',
                'statuses' => ['New', 'For Clarification', 'Approved'],
                'tasks' => $allTasks->whereIn('status', ['New', 'For Clarification', 'Approved'])
            ],
            'in_progress' => [
                'title' => 'In Progress',
                'statuses' => ['Assigned', 'In Progress', 'Blocked'],
                'tasks' => $allTasks->whereIn('status', ['Assigned', 'In Progress', 'Blocked'])
            ],
            'qa_testing' => [
                'title' => 'QA Testing',
                'statuses' => ['For QA UAT'],
                'tasks' => $allTasks->where('status', 'For QA UAT')
            ],
            'implementation_testing' => [
                'title' => 'Implementation Testing',
                'statuses' => ['For Implementation UAT'],
                'tasks' => $allTasks->where('status', 'For Implementation UAT')
            ],
            'done' => [
                'title' => 'Done',
                'statuses' => ['Done'],
                'tasks' => $allTasks->where('status', 'Done')
            ],
        ];

        // Get sites for filter
        if ($user->hasRole('Admin')) {
            $sites = Site::active()->orderBy('name')->get();
        } else {
            $sites = $user->sites;
        }

        // Get users (developers) for filter
        if ($user->hasRole('Admin')) {
            $developers = User::whereJsonContains('roles', 'Developer')
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        } else {
            $userSiteIds = $user->sites->pluck('id')->toArray();
            $developers = User::whereJsonContains('roles', 'Developer')
                ->where('is_active', true)
                ->whereHas('sites', function($q) use ($userSiteIds) {
                    $q->whereIn('sites.id', $userSiteIds);
                })
                ->orderBy('name')
                ->get();
        }

        return view('tasks.kanban', compact('columns', 'sites', 'developers'));
    }

    /**
     * Update task status via AJAX (for Kanban drag-and-drop)
     */
    public function updateStatus(Request $request, Task $task)
    {
        $validated = $request->validate([
            'status' => 'required|in:New,For Clarification,Approved,Assigned,In Progress,Blocked,For QA UAT,For Implementation UAT,Done,Rejected,Cancelled,On Hold',
        ]);

        $user = auth()->user();
        $oldStatus = $task->status;
        $newStatus = $validated['status'];

        // Role-based status change validation
        if (!$user->hasAnyRole(['Admin', 'Lead'])) {
            // Developer role: Can only move between Backlog, In Progress, and QA Testing
            if ($user->hasRole('Developer')) {
                $allowedStatuses = ['New', 'For Clarification', 'Approved', 'Assigned', 'In Progress', 'Blocked', 'For QA UAT'];
                if (!in_array($newStatus, $allowedStatuses)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Developers can only move tasks between Backlog, In Progress, and QA Testing columns.'
                    ], 403);
                }
            }
            // QA role: Can only move tasks to Implementation Testing
            elseif ($user->hasRole('QA')) {
                if ($newStatus !== 'For Implementation UAT') {
                    return response()->json([
                        'success' => false,
                        'message' => 'QA can only move tasks to Implementation Testing column.'
                    ], 403);
                }
                // QA can only move from QA Testing column
                if ($oldStatus !== 'For QA UAT') {
                    return response()->json([
                        'success' => false,
                        'message' => 'QA can only move tasks from QA Testing column.'
                    ], 403);
                }
            }
            // Implementation role: Can only move tasks to Done
            elseif ($user->hasRole('Implementation')) {
                if ($newStatus !== 'Done') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Implementation can only move tasks to Done column.'
                    ], 403);
                }
                // Implementation can only move from Implementation Testing column
                if ($oldStatus !== 'For Implementation UAT') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Implementation can only move tasks from Implementation Testing column.'
                    ], 403);
                }
            }
        }

        // Log status change to history
        $this->logTaskHistory($task, 'status', $oldStatus, $newStatus);

        $task->update(['status' => $newStatus]);

        return response()->json([
            'success' => true,
            'message' => "Task {$task->task_no} moved from {$oldStatus} to {$newStatus}",
            'task' => $task->load(['siteProject.site', 'siteProject.project', 'developer', 'qa'])
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Task $task)
    {
        $user = auth()->user();

        // Get site projects filtered by user's assigned sites
        if ($user->hasRole('Admin')) {
            $siteProjects = SiteProject::with(['site', 'project'])
                ->whereHas('site', function($q) {
                    $q->where('is_active', true);
                })
                ->whereHas('project', function($q) {
                    $q->where('is_active', true);
                })
                ->orderBy('site_id')
                ->get();

            // Get all developers and QA for assignment
            $developers = User::whereJsonContains('roles', 'Developer')
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
            $qaUsers = User::whereJsonContains('roles', 'QA')
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        } else {
            $userSiteIds = $user->sites->pluck('id')->toArray();
            $siteProjects = SiteProject::with(['site', 'project'])
                ->whereIn('site_id', $userSiteIds)
                ->whereHas('site', function($q) {
                    $q->where('is_active', true);
                })
                ->whereHas('project', function($q) {
                    $q->where('is_active', true);
                })
                ->orderBy('site_id')
                ->get();

            // Get developers and QA from user's assigned sites
            $developers = User::whereJsonContains('roles', 'Developer')
                ->where('is_active', true)
                ->whereHas('sites', function($q) use ($userSiteIds) {
                    $q->whereIn('sites.id', $userSiteIds);
                })
                ->orderBy('name')
                ->get();
            $qaUsers = User::whereJsonContains('roles', 'QA')
                ->where('is_active', true)
                ->whereHas('sites', function($q) use ($userSiteIds) {
                    $q->whereIn('sites.id', $userSiteIds);
                })
                ->orderBy('name')
                ->get();
        }

        // Available options
        $types = ['New Feature', 'Enhancement', 'Bug', 'Data Fix', 'Support', 'Report'];
        $priorities = ['Urgent', 'High', 'Medium', 'Low'];
        $statuses = ['New', 'For Clarification', 'Approved', 'Assigned', 'In Progress', 'Blocked', 'For QA UAT', 'For Implementation UAT', 'Done', 'Rejected', 'Cancelled', 'On Hold'];

        return view('tasks.edit', compact('task', 'siteProjects', 'types', 'priorities', 'statuses', 'developers', 'qaUsers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'site_project_id' => 'required|exists:site_projects,id',
            'type' => 'required|in:New Feature,Enhancement,Bug,Data Fix,Support,Report',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:Urgent,High,Medium,Low',
            'status' => 'required|in:New,For Clarification,Approved,Assigned,In Progress,Blocked,For QA UAT,For Implementation UAT,Done,Rejected,Cancelled,On Hold',
            'assigned_to' => 'nullable|exists:users,id',
            'qa_assigned_to' => 'nullable|exists:users,id',
            'target_date' => 'nullable|date',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240', // 10MB max per file
        ]);

        $updateData = [
            'site_project_id' => $validated['site_project_id'],
            'type' => $validated['type'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'priority' => $validated['priority'],
            'status' => $validated['status'],
        ];

        // Update assignment fields if provided
        if ($request->filled('assigned_to')) {
            $updateData['assigned_to'] = $validated['assigned_to'];
            if (!$task->date_assigned) {
                $updateData['date_assigned'] = Carbon::now();
            }
        }

        if ($request->filled('qa_assigned_to')) {
            $updateData['qa_assigned_to'] = $validated['qa_assigned_to'];
        }

        if ($request->filled('target_date')) {
            $updateData['target_date'] = $validated['target_date'];
        }

        // Recalculate SLA if priority changed
        if ($task->priority !== $validated['priority']) {
            $slaRule = SlaRule::where('priority', $validated['priority'])->first();
            $businessDays = $slaRule ? $slaRule->business_days : 5;
            $updateData['sla_due_date'] = $this->addBusinessDays($task->date_requested, $businessDays);
        }

        $task->update($updateData);

        // Handle file uploads
        $this->handleFileUploads($request, $task);

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Task updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Add business days to a date (excluding weekends)
     */
    private function addBusinessDays($startDate, $businessDays)
    {
        $currentDate = Carbon::parse($startDate);
        $addedDays = 0;

        while ($addedDays < $businessDays) {
            $currentDate->addDay();
            // Skip weekends
            if ($currentDate->isWeekday()) {
                $addedDays++;
            }
        }

        return $currentDate;
    }

    /**
     * Log task field changes to history
     */
    private function logTaskHistory(Task $task, $field, $oldValue, $newValue, $reason = null)
    {
        // Skip if values are the same
        if ($oldValue == $newValue) {
            return;
        }

        // Convert objects/dates to strings for storage
        if ($oldValue instanceof \DateTime || $oldValue instanceof Carbon) {
            $oldValue = $oldValue->format('Y-m-d H:i:s');
        }
        if ($newValue instanceof \DateTime || $newValue instanceof Carbon) {
            $newValue = $newValue->format('Y-m-d H:i:s');
        }

        // Get user names for assignment fields
        if (in_array($field, ['assigned_to', 'qa_assigned_to', 'requested_by', 'rejected_by'])) {
            if ($oldValue) {
                $oldUser = User::find($oldValue);
                $oldValue = $oldUser ? $oldUser->name : 'User #' . $oldValue;
            }
            if ($newValue) {
                $newUser = User::find($newValue);
                $newValue = $newUser ? $newUser->name : 'User #' . $newValue;
            }
        }

        TaskHistory::create([
            'task_id' => $task->id,
            'field' => $field,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'reason' => $reason,
            'changed_by' => auth()->id(),
            'changed_at' => now(),
        ]);
    }

    /**
     * Handle file uploads for a task
     */
    private function handleFileUploads(Request $request, Task $task)
    {
        if (!$request->hasFile('attachments')) {
            return;
        }

        foreach ($request->file('attachments') as $file) {
            // Validate file size (10MB max)
            if ($file->getSize() > 10 * 1024 * 1024) {
                continue; // Skip files larger than 10MB
            }

            // Generate unique filename
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $fileName = pathinfo($originalName, PATHINFO_FILENAME);
            $uniqueFileName = $fileName . '_' . time() . '_' . uniqid() . '.' . $extension;

            // Store file in storage/app/task_attachments
            $path = $file->storeAs('task_attachments', $uniqueFileName);

            // Create attachment record
            TaskAttachment::create([
                'task_id' => $task->id,
                'file_name' => $originalName,
                'file_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
                'storage_path' => $path,
                'uploaded_by' => auth()->id(),
                'uploaded_at' => now(),
            ]);
        }
    }

    /**
     * Download a task attachment
     */
    public function downloadAttachment(TaskAttachment $attachment)
    {
        // Check if attachment exists and is not deleted
        if ($attachment->is_deleted) {
            abort(404, 'Attachment not found');
        }

        // Check if file exists in storage
        if (!Storage::exists($attachment->storage_path)) {
            abort(404, 'File not found');
        }

        // Download the file
        return Storage::download($attachment->storage_path, $attachment->file_name);
    }
}
