<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Site;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeUserMail;
use App\Mail\PasswordResetMail;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with('sites');

        // Filter by role if requested
        if ($request->has('role') && $request->role) {
            $query->whereJsonContains('roles', $request->role);
        }

        $users = $query->orderBy('name')->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sites = Site::active()->orderBy('name')->get();
        $availableRoles = ['Admin', 'Lead', 'Developer', 'QA', 'Implementation'];

        return view('admin.users.create', compact('sites', 'availableRoles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'roles' => 'required|array|min:1',
            'roles.*' => 'in:Admin,Lead,Developer,QA,Implementation',
            'site_ids' => 'nullable|array',
            'site_ids.*' => 'exists:sites,id',
            'is_active' => 'boolean',
        ]);

        // Generate a random password
        $password = Str::random(12);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($password),
            'roles' => $validated['roles'],
            'is_active' => $request->has('is_active'),
        ]);

        // Attach sites
        if (!empty($validated['site_ids'])) {
            $user->sites()->attach($validated['site_ids']);
        }

        // Send welcome email with password
        try {
            Mail::to($user->email)->send(new WelcomeUserMail($user, $password));
            return redirect()->route('admin.users.index')
                ->with('success', "User created successfully. Welcome email sent to {$user->email}");
        } catch (\Exception $e) {
            return redirect()->route('admin.users.index')
                ->with('warning', "User created successfully, but email could not be sent. Temporary password: {$password} (Please configure email settings or manually send this to the user)");
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load(['sites', 'assignedTasks', 'requestedTasks']);
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $sites = Site::active()->orderBy('name')->get();
        $availableRoles = ['Admin', 'Lead', 'Developer', 'QA', 'Implementation'];

        return view('admin.users.edit', compact('user', 'sites', 'availableRoles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'roles' => 'required|array|min:1',
            'roles.*' => 'in:Admin,Lead,Developer,QA,Implementation',
            'site_ids' => 'nullable|array',
            'site_ids.*' => 'exists:sites,id',
            'is_active' => 'boolean',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'roles' => $validated['roles'],
            'is_active' => $request->has('is_active'),
        ];

        // Update password if provided
        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        // Sync sites
        $user->sites()->sync($validated['site_ids'] ?? []);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Check if user has assigned tasks
        if ($user->assignedTasks()->whereNotIn('status', ['Done', 'Rejected', 'Cancelled'])->count() > 0) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Cannot delete user with active assigned tasks. Please reassign them first.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Reset and resend password to user
     */
    public function resetPassword(User $user)
    {
        // Generate a new random password
        $password = Str::random(12);

        // Update user's password
        $user->update([
            'password' => Hash::make($password),
        ]);

        // Send password reset email
        try {
            Mail::to($user->email)->send(new PasswordResetMail($user, $password));
            return redirect()->back()
                ->with('success', "Password reset successfully. Email sent to {$user->email}");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('warning', "Password reset successfully, but email could not be sent. New password: {$password} (Please configure email settings or manually send this to the user)");
        }
    }
}
