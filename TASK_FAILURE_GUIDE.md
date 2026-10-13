# Task Failure Feature Guide

## Overview
The task failure feature allows QA and Implementation testers to reject tasks that don't meet quality standards and automatically create rework tasks for developers.

## How to Use

### 1. Fail QA Testing

**Who can use it:**
- Users with QA role
- Users with Lead role
- Users with Admin role

**When it appears:**
- Task must be in "For QA UAT" status (QA Testing column on Kanban board)
- Button appears in the "Actions" card on the RIGHT SIDEBAR of the task details page

**Steps:**
1. Navigate to a task in "For QA UAT" status
2. Look at the RIGHT sidebar (not the main content area)
3. Find the "Actions" card
4. Click the **"Fail QA Testing"** button (red button with X icon)
5. A modal will appear asking for rejection reason
6. Enter specific details about what failed
7. Click "Fail QA & Create Rework Task"

**What happens:**
- Original task status → "Blocked"
- New rework task created with "[REWORK]" prefix
- Rework task assigned to original developer
- Target date: 2 days
- Priority: Same as original task

### 2. Fail Implementation Testing

**Who can use it:**
- Users with Implementation role
- Users with Lead role
- Users with Admin role

**When it appears:**
- Task must be in "For Implementation UAT" status (Implementation Testing column)
- Button appears in the "Actions" card on the RIGHT SIDEBAR

**Steps:**
1. Navigate to a task in "For Implementation UAT" status
2. Look at the RIGHT sidebar
3. Find the "Actions" card
4. Click **"Fail Implementation Testing"** button
5. Enter rejection reason in modal
6. Click "Fail Implementation & Create Rework Task"

**What happens:**
- Original task status → "Blocked"
- New HIGH priority rework task created
- Rework task assigned to original developer
- Target date: 1 day (urgent!)
- Priority: HIGH (bumped up for implementation failures)

## Testing the Feature

### Step 1: Move a task to the right status
1. Go to Kanban board
2. Drag a task to "QA Testing" column (status becomes "For QA UAT")

### Step 2: View the task
1. Click on the task to open details
2. Look at the RIGHT sidebar

### Step 3: Fail the test
1. Click "Fail QA Testing" button
2. Enter reason: "Button not working on mobile devices"
3. Submit

### Step 4: Check results
1. You'll be redirected to the new rework task
2. Original task will show as "Blocked"
3. New task will have "[REWORK]" prefix
4. Check "Related Tasks" card to see parent-child relationship

## Location Guide

```
Task Details Page Layout:
┌─────────────────────────────────────────────────────┐
│ MAIN CONTENT (Left)        │  SIDEBAR (Right)       │
│                             │                        │
│ - Task Information          │ ┌─────────────────┐   │
│ - Description               │ │ Actions Card    │   │
│ - Comments                  │ │ =============== │   │
│                             │ │ ✓ Approve       │   │
│                             │ │ ✗ Reject        │   │
│                             │ │ 👤 Assign       │   │
│                             │ │ ❌ Fail QA ←    │   │
│                             │ │ ❌ Fail Implem  │   │
│                             │ └─────────────────┘   │
│                             │                        │
│                             │ ┌─────────────────┐   │
│                             │ │ Attachments     │   │
│                             │ └─────────────────┘   │
│                             │                        │
│                             │ ┌─────────────────┐   │
│                             │ │ Movement History│   │
│                             │ └─────────────────┘   │
└─────────────────────────────────────────────────────┘
```

## Current Test Task

**Task:** REQ-2026-0001
**Status:** For QA UAT
**Action:** Ready for QA failure testing

To test:
1. Login as QA/Lead/Admin user
2. Go to http://localhost:8000/tasks/1
3. Look at right sidebar "Actions" card
4. Click "Fail QA Testing" button

## Troubleshooting

**Problem:** "I don't see the Fail QA Testing button"

**Solutions:**
1. Check your role - must be QA, Lead, or Admin
2. Check task status - must be "For QA UAT"
3. Check the RIGHT sidebar, not main content
4. Scroll down in the sidebar - button might be below
5. Clear browser cache and refresh

**Problem:** "Button is greyed out or not clickable"

**Solutions:**
1. Ensure task is in correct status
2. Check browser console for JavaScript errors
3. Ensure you're logged in with correct role

**Problem:** "Modal doesn't open when I click"

**Solutions:**
1. Check browser console for errors
2. Ensure Bootstrap JavaScript is loaded
3. Hard refresh page (Ctrl+F5)
