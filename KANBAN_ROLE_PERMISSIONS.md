# Kanban Board - Role-Based Permissions

This document explains the role-based drag-and-drop permissions implemented in the Kanban board.

---

## Column Structure

The Kanban board has 5 columns:

| Column | Statuses | Description |
|--------|----------|-------------|
| **Backlog** | New, For Clarification, Approved | New tasks and tasks awaiting clarification |
| **In Progress** | Assigned, In Progress, Blocked | Tasks being actively worked on |
| **QA Testing** | For QA UAT | Tasks ready for QA testing |
| **Implementation Testing** | For Implementation UAT | Tasks ready for implementation/user acceptance testing |
| **Done** | Done | Completed tasks |

---

## Role-Based Permissions

### 👨‍💻 Developer Role

**Can move tasks to:**
- ✅ Backlog (New, For Clarification, Approved)
- ✅ In Progress (Assigned, In Progress, Blocked)
- ✅ QA Testing (For QA UAT)

**Cannot move tasks to:**
- ❌ Implementation Testing
- ❌ Done

**Workflow:**
Developers can move tasks from Backlog → In Progress → QA Testing

**Visual Feedback:**
- Allowed columns: Green border
- Disabled columns: Red border with 🔒 lock icon

---

### 🧪 QA Role

**Can move tasks to:**
- ✅ Implementation Testing (For Implementation UAT)

**Cannot move tasks to:**
- ❌ Backlog
- ❌ In Progress
- ❌ QA Testing (can view but not drop from other columns)
- ❌ Done

**Restrictions:**
- Can ONLY move tasks FROM QA Testing column
- Can ONLY move tasks TO Implementation Testing column

**Workflow:**
QA Testing → Implementation Testing

**Visual Feedback:**
- Only Implementation Testing column is enabled
- All other columns show 🔒 lock icon

---

### 🚀 Implementation Role

**Can move tasks to:**
- ✅ Done

**Cannot move tasks to:**
- ❌ Backlog
- ❌ In Progress
- ❌ QA Testing
- ❌ Implementation Testing (can view but not drop from other columns)

**Restrictions:**
- Can ONLY move tasks FROM Implementation Testing column
- Can ONLY move tasks TO Done column

**Workflow:**
Implementation Testing → Done

**Visual Feedback:**
- Only Done column is enabled
- All other columns show 🔒 lock icon

---

### 👨‍💼 Admin / Lead Roles

**Can move tasks to:**
- ✅ ALL columns (unrestricted access)

**No restrictions:**
- Can move tasks freely between any columns
- Can override any status changes
- Full control over task workflow

**Visual Feedback:**
- All columns are enabled (green borders)
- No lock icons shown

---

## Validation Layers

The system implements **two layers of validation**:

### 1. Client-Side Validation (JavaScript)

**Purpose:** Provide immediate visual feedback

**Features:**
- Prevents drag-and-drop to restricted columns
- Shows disabled columns with red border and lock icon
- Displays opacity change when attempting invalid move
- Shows error notification if move is attempted

**Code Location:** `resources/views/tasks/kanban.blade.php` (lines 368-539)

**Key Functions:**
- `canMoveToColumn(columnKey)` - Checks if user can drop in column
- `markDisabledColumns()` - Visually marks restricted columns
- `onMove()` callback - Validates move before allowing drop

---

### 2. Server-Side Validation (PHP)

**Purpose:** Security and data integrity

**Features:**
- Validates role permissions on task status update
- Returns 403 error for unauthorized moves
- Prevents API manipulation attempts
- Logs all status changes

**Code Location:** `app/Http/Controllers/TaskController.php` (lines 472-535)

**Validation Logic:**
```php
// Example for Developer role
if ($user->hasRole('Developer')) {
    $allowedStatuses = ['New', 'For Clarification', 'Approved',
                       'Assigned', 'In Progress', 'Blocked', 'For QA UAT'];
    if (!in_array($newStatus, $allowedStatuses)) {
        return response()->json([
            'success' => false,
            'message' => 'Developers can only move tasks between Backlog, In Progress, and QA Testing columns.'
        ], 403);
    }
}
```

---

## Error Messages

### Developer
- "Developers can only move tasks between Backlog, In Progress, and QA Testing columns."

### QA
- "QA can only move tasks to Implementation Testing column."
- "QA can only move tasks from QA Testing column."

### Implementation
- "Implementation can only move tasks to Done column."
- "Implementation can only move tasks from Implementation Testing column."

---

## Visual Indicators

### Allowed Columns
```css
/* Green theme - can drop here */
border-bottom: 2px solid #8BC53F;
opacity: 1;
pointer-events: all;
```

### Disabled Columns
```css
/* Red theme - cannot drop here */
.column-disabled {
    opacity: 0.6;
    pointer-events: none;
}

.column-disabled .kanban-column-header {
    background: linear-gradient(135deg, rgba(220, 53, 69, 0.08) 0%, rgba(220, 53, 69, 0.08) 100%);
    border-bottom: 2px solid #dc3545;
}

/* Lock icon */
.column-disabled::after {
    content: '🔒';
    position: absolute;
    top: 10px;
    right: 10px;
    font-size: 1.5rem;
    opacity: 0.5;
}
```

---

## Task Flow Examples

### Example 1: Developer Workflow

```
1. Developer receives task in Backlog (New)
2. Drags to In Progress (Assigned)
3. Works on task (In Progress)
4. Completes development
5. Drags to QA Testing (For QA UAT)
✅ Task now visible to QA team
```

### Example 2: QA Workflow

```
1. QA sees task in QA Testing (For QA UAT)
2. Performs testing
3. If passes: Drags to Implementation Testing (For Implementation UAT)
4. If fails: Moves back to In Progress (requires Admin/Lead)
✅ Task now visible to Implementation team
```

### Example 3: Implementation Workflow

```
1. Implementation sees task in Implementation Testing (For Implementation UAT)
2. Performs final validation
3. If approved: Drags to Done
✅ Task marked as complete
```

### Example 4: Admin Override

```
1. Task stuck in wrong column
2. Admin can drag from anywhere to anywhere
3. Can fix workflow issues
4. Can reset task statuses
✅ Full control over task lifecycle
```

---

## Testing the Permissions

### Test as Developer

1. Login as user with Developer role
2. Go to Kanban board
3. Try to drag task to Implementation Testing
4. ❌ Should be prevented (visual opacity change)
5. Try to drag task from Backlog to QA Testing
6. ✅ Should work

### Test as QA

1. Login as user with QA role
2. Go to Kanban board
3. Notice only Implementation Testing is enabled
4. Try to drag task from In Progress
5. ❌ Should be prevented
6. Drag task from QA Testing to Implementation Testing
7. ✅ Should work

### Test as Implementation

1. Login as user with Implementation role
2. Go to Kanban board
3. Notice only Done column is enabled
4. Try to drag task from Backlog
5. ❌ Should be prevented
6. Drag task from Implementation Testing to Done
7. ✅ Should work

### Test as Admin

1. Login as Admin
2. Go to Kanban board
3. Notice all columns are enabled
4. Drag task anywhere
5. ✅ All moves should work

---

## API Endpoint

**Route:** `POST /tasks/{task}/update-status`

**Request Body:**
```json
{
    "status": "In Progress"
}
```

**Success Response (200):**
```json
{
    "success": true,
    "message": "Task TASK-123 moved from New to In Progress",
    "task": { /* task object */ }
}
```

**Error Response (403):**
```json
{
    "success": false,
    "message": "Developers can only move tasks between Backlog, In Progress, and QA Testing columns."
}
```

---

## Troubleshooting

### Issue: Can't drag any tasks

**Possible Causes:**
1. Not logged in
2. No roles assigned to user
3. JavaScript not loaded

**Solution:**
- Check user has at least one role
- Check browser console for errors
- Verify SortableJS is loaded

### Issue: All columns disabled

**Possible Cause:** User has role not defined in permissions

**Solution:**
- User needs Developer, QA, Implementation, Admin, or Lead role
- Check `User::roles` JSON field

### Issue: Can drag to restricted column

**Possible Causes:**
1. Server-side validation will catch it
2. Cache issue

**Solution:**
- Clear browser cache
- Run `php artisan cache:clear`
- Check role assignments

---

## Future Enhancements

Potential improvements:

1. **Role Combinations**
   - Handle users with multiple roles
   - Implement role hierarchy

2. **Custom Workflows**
   - Allow admins to configure workflows
   - Per-project workflow rules

3. **Audit Trail**
   - Log all status changes
   - Track who moved tasks and when

4. **Notifications**
   - Email when task reaches your column
   - Slack/Teams integration

5. **Bulk Operations**
   - Move multiple tasks at once
   - Batch status updates

---

## Summary

| Role | Allowed Columns | Workflow |
|------|----------------|----------|
| **Developer** | Backlog, In Progress, QA Testing | Dev work and QA submission |
| **QA** | Implementation Testing (from QA Testing only) | QA approval |
| **Implementation** | Done (from Implementation Testing only) | Final approval |
| **Admin/Lead** | ALL | Full control |

**Key Points:**
- ✅ Two-layer validation (client + server)
- ✅ Visual feedback with lock icons
- ✅ Clear error messages
- ✅ Responsive drag-and-drop
- ✅ Mobile compatible
- ✅ Security enforced server-side

---

**Last Updated:** 2026-10-13
**Version:** 1.0
**Status:** ✅ Implemented and tested
