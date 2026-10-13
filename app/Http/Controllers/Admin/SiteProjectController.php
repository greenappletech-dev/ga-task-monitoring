<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SiteProject;
use App\Models\Site;
use App\Models\Project;
use App\Models\User;

class SiteProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $siteProjects = SiteProject::with(['site', 'project', 'lead'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.site-projects.index', compact('siteProjects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sites = Site::active()->orderBy('name')->get();
        $projects = Project::active()->orderBy('name')->get();
        $leads = User::whereJsonContains('roles', 'Lead')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.site-projects.create', compact('sites', 'projects', 'leads'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'site_id' => 'required|exists:sites,id',
            'project_id' => 'required|exists:projects,id|unique:site_projects,project_id,NULL,id,site_id,' . $request->site_id,
            'lead_user_id' => 'nullable|exists:users,id',
            'version' => 'nullable|string|max:255',
            'go_live_date' => 'nullable|date',
            'status' => 'required|in:Development,Live,Maintenance',
        ]);

        SiteProject::create($validated);

        return redirect()->route('admin.site-projects.index')
            ->with('success', 'Site project created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SiteProject $siteProject)
    {
        $siteProject->load(['site', 'project', 'lead', 'tasks']);
        return view('admin.site-projects.show', compact('siteProject'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SiteProject $siteProject)
    {
        $sites = Site::active()->orderBy('name')->get();
        $projects = Project::active()->orderBy('name')->get();
        $leads = User::whereJsonContains('roles', 'Lead')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.site-projects.edit', compact('siteProject', 'sites', 'projects', 'leads'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SiteProject $siteProject)
    {
        $validated = $request->validate([
            'site_id' => 'required|exists:sites,id',
            'project_id' => 'required|exists:projects,id|unique:site_projects,project_id,' . $siteProject->id . ',id,site_id,' . $request->site_id,
            'lead_user_id' => 'nullable|exists:users,id',
            'version' => 'nullable|string|max:255',
            'go_live_date' => 'nullable|date',
            'status' => 'required|in:Development,Live,Maintenance',
        ]);

        $siteProject->update($validated);

        return redirect()->route('admin.site-projects.index')
            ->with('success', 'Site project updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SiteProject $siteProject)
    {
        // Check if site project has tasks
        if ($siteProject->tasks()->count() > 0) {
            return redirect()->route('admin.site-projects.index')
                ->with('error', 'Cannot delete site project with existing tasks.');
        }

        $siteProject->delete();

        return redirect()->route('admin.site-projects.index')
            ->with('success', 'Site project deleted successfully.');
    }
}
