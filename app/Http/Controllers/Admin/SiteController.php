<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Site;

class SiteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sites = Site::withCount('siteProjects')->orderBy('name')->paginate(20);
        return view('admin.sites.index', compact('sites'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.sites.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:sites,code',
            'name' => 'required|string|max:255',
            'client' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        Site::create($validated);

        return redirect()->route('admin.sites.index')
            ->with('success', 'Site created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Site $site)
    {
        $site->load(['siteProjects.project', 'siteProjects.lead']);
        return view('admin.sites.show', compact('site'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Site $site)
    {
        return view('admin.sites.edit', compact('site'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Site $site)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:sites,code,' . $site->id,
            'name' => 'required|string|max:255',
            'client' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $site->update($validated);

        return redirect()->route('admin.sites.index')
            ->with('success', 'Site updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Site $site)
    {
        // Check if site has projects
        if ($site->siteProjects()->count() > 0) {
            return redirect()->route('admin.sites.index')
                ->with('error', 'Cannot delete site with existing projects.');
        }

        $site->delete();

        return redirect()->route('admin.sites.index')
            ->with('success', 'Site deleted successfully.');
    }
}
