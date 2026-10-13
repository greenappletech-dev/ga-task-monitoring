# How to Test "Fail QA & Create Rework Task" Feature

## 🔴 Problems Found with Your Current Task (REQ-2026-0001):

1. ❌ **No Developer Assigned** - The task has no developer
2. ❌ **No QA Assigned** - The task has no QA
3. ❌ **No QA Users in System** - You have 0 QA users

## ✅ How to Fix and Test:

### Step 1: Create QA User (REQUIRED)

1. Go to: **http://localhost:8000/admin/users/create**
2. Fill in:
   - **Name**: QA Tester
   - **Email**: qa@test.com
   - **Password**: (will be auto-generated)
   - **Roles**: Check ✅ **QA**
   - **Sites**: Assign to Pagadian site
   - **Is Active**: ✅ Yes
3. Click "Create User"

### Step 2: Create a Proper Test Task

#### Option A: Create New Task (Recommended)
1. Go to: **http://localhost:8000/tasks/create**
2. Fill in:
   - **Site/Project**: Pagadian - HRIS
   - **Type**: Enhancement
   - **Title**: Test QA Failure Feature
   - **Description**: This is a test task to verify QA failure functionality
   - **Source**: Internal Development (auto-approves)
   - **Priority**: Medium
   - **Assign Developer**: Miguel Bautista ✅ (IMPORTANT!)
   - **Assign QA**: QA Tester ✅ (IMPORTANT!)
   - **Target Date**: (any future date)
3. Click "Create Task"

#### Option B: Fix Existing Task REQ-2026-0001
1. First, change status from "For QA UAT" to "Approved":
   - Go to Kanban: http://localhost:8000/kanban
   - Drag REQ-2026-0001 from QA Testing to Backlog
   - Status will change to "Approved"

2. Assign Developer and QA:
   - Click on the task
   - Click "Assign Developer & QA" button
   - Select Miguel Bautista as Developer
   - Select QA Tester as QA
   - Set target date
   - Click "Assign Task"

3. Move to QA Testing:
   - Go back to Kanban
   - Drag the task through: In Progress → QA Testing
   - Status becomes "For QA UAT"

### Step 3: Log in as QA User

1. Logout from current account
2. Login with QA credentials:
   - Email: **qa@test.com**
   - Password: (check email or ask admin for password)

### Step 4: Fail the QA Test

1. Go to the task (either new test task or REQ-2026-0001)
2. **Look at RIGHT SIDEBAR** - you'll see "Actions" card
3. **Click RED "Fail QA Testing" button**
4. **A modal will appear**
5. **Enter rejection reason**:
   ```
   Login button does not work on mobile devices.
   Error message: "Invalid credentials" appears even with correct password.
   Browser console shows 401 error.
   ```
6. **Click "Fail QA & Create Rework Task"**

### Step 5: Verify Results

**You should see:**

1. ✅ Redirected to NEW rework task
2. ✅ New task number like "TASK-67271A2B3C4D5"
3. ✅ Title: `[REWORK] Test QA Failure Feature`
4. ✅ Status: "Assigned"
5. ✅ Priority: Same as original (or HIGH for implementation failures)
6. ✅ Assigned to: Miguel Bautista (original developer)
7. ✅ Description includes rejection reason

**Check original task:**
1. Go back to original task
2. ✅ Status changed to "Blocked"
3. ✅ "Related Tasks" card shows the new rework task
4. ✅ Movement History shows the failure with rejection reason

## 🎯 Complete Test Workflow:

```
1. Create QA User → qa@test.com
   ↓
2. Create Task → Assign Developer & QA
   ↓
3. Move to QA Testing (drag on Kanban)
   ↓
4. Login as QA user
   ↓
5. Open task → Click "Fail QA Testing"
   ↓
6. Enter reason → Submit
   ↓
7. ✅ Rework task created!
```

## 📋 Checklist Before Testing:

- [ ] At least 1 QA user exists and is active
- [ ] At least 1 Developer user exists and is active
- [ ] Task status is "For QA UAT"
- [ ] Task has Developer assigned (assigned_to field)
- [ ] Task has QA assigned (qa_assigned_to field)
- [ ] Logged in as QA, Lead, or Admin role

## ⚠️ Common Issues:

**Error: "Cannot fail QA testing: Task has no developer assigned"**
- **Fix**: Assign a developer to the task first using "Assign Developer & QA" button

**Error: "You do not have permission to fail QA testing"**
- **Fix**: Login as user with QA, Lead, or Admin role

**Error: "Only tasks in QA Testing can be failed"**
- **Fix**: Move task to QA Testing column on Kanban board

**Button doesn't appear:**
- **Fix**: Task must be in "For QA UAT" status
- **Fix**: Must be logged in with QA/Lead/Admin role

## 🧪 Quick Test Command:

Run this to check your current setup:
```bash
cd C:\xampp\htdocs\ga_tracker\project-tracker
php artisan tinker
```

Then run:
```php
// Check QA users
$qa = App\Models\User::whereJsonContains('roles', 'QA')->where('is_active', true)->get();
echo "QA Users: " . $qa->count() . "\n";

// Check developers
$devs = App\Models\User::whereJsonContains('roles', 'Developer')->where('is_active', true)->get();
echo "Developers: " . $devs->count() . "\n";

// Check test task
$task = App\Models\Task::where('status', 'For QA UAT')->first();
if ($task) {
    echo "Task: {$task->task_no}\n";
    echo "Dev assigned: " . ($task->assigned_to ? "YES" : "NO") . "\n";
    echo "QA assigned: " . ($task->qa_assigned_to ? "YES" : "NO") . "\n";
}
```

## ✅ Expected Result:

After clicking "Fail QA & Create Rework Task", you should see a success message:

```
QA Test Failed. New rework task TASK-XXXXX has been created and assigned to the developer.
```

And you'll be on the new rework task page showing all the details!
