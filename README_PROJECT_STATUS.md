# Project & Request Tracker - Laravel Application

## Project Status: Foundation Complete ✓

This is a comprehensive Project & Request Tracker built with Laravel 8 for XAMPP, based on the detailed requirements document.

---

## What Has Been Built So Far

### ✅ Phase 1: Foundation (COMPLETED)
- [x] Laravel 8 project created and configured
- [x] Database configuration for XAMPP MySQL
- [x] Database `project_tracker` created
- [x] All 19 database tables created via migrations
- [x] SLA rules seeded (Urgent=0, High=3, Medium=5, Low=10 business days)
- [x] Default admin user created
- [x] All Eloquent models generated

### Database Structure Created

The following tables have been created:

**Core Tables:**
- `users` - User management with roles (Admin, Lead, Developer, QA, Implementation)
- `sites` - Client sites/locations
- `projects` - Products/systems (e.g., HRIS, Payroll)
- `site_projects` - Deployment of projects at specific sites
- `user_site_access` - Controls which sites users can access

**Task Management:**
- `tasks` - Main task/request tracking with full workflow
- `task_uat` - QA and Implementation UAT results
- `target_date_requests` - Developer requests to change target dates
- `task_assignees` - Additional assignees beyond primary developer
- `task_comments` - Comments and discussions
- `task_attachments` - File uploads (documents, screenshots, etc.)
- `task_history` - Complete audit trail of changes
- `notifications` - In-app notifications

**Configuration:**
- `sla_rules` - Priority-based SLA targets
- `holidays` - Business days configuration for SLA calculation

---

## Default Credentials

**Admin User:**
- Email: `admin@projecttracker.com`
- Password: `password`

---

## Accessing the Application

1. Make sure XAMPP is running (Apache and MySQL)

2. Access the application at:
   ```
   http://localhost/ga_tracker/project-tracker/public
   ```

3. Database connection details (already configured in `.env`):
   - Database: `project_tracker`
   - Username: `root`
   - Password: (empty)

---

## Key Features Implemented (Database Level)

### User Roles System
Users can have multiple roles:
- **Admin**: Full system access
- **Lead**: Review requests, assign developers, set target dates
- **Developer**: Work on tasks, update status
- **QA**: Test completed work
- **Implementation**: Submit requests, do final UAT

### Task Workflow Statuses
```
New → For Clarification → Approved → Assigned → In Progress
    → For QA UAT → For Implementation UAT → Done

Special states: Blocked, Rejected, Cancelled, On Hold
```

### Two-Stage UAT Process
1. **QA UAT**: Technical testing by QA team
2. **Implementation UAT**: Business acceptance by Implementation team

Either stage can return the task to "In Progress" with findings.

### Priority & SLA System
- **Urgent**: 0 business days (same day)
- **High**: 3 business days
- **Medium**: 5 business days
- **Low**: 10 business days

SLA clock pauses when a task is Blocked.

### Site-Based Access Control
- Users are assigned to specific sites
- Implementation users see ALL requests for their assigned sites
- Separate task lists for same project at different sites

---

## Next Steps

### Phase 2: Authentication & UI Foundation
- [ ] Set up Laravel UI with authentication
- [ ] Create role-based middleware
- [ ] Build main layout with sidebar navigation
- [ ] Implement user session management

### Phase 3: Admin Interfaces
- [ ] Sites management (Add/Edit/Deactivate)
- [ ] Projects management
- [ ] Site Projects assignment
- [ ] Users management with role selection
- [ ] Bulk import from Excel/CSV

### Phase 4: Core Features
- [ ] Implementation Request Entry form
- [ ] Kanban Board (main view with drag & drop)
- [ ] Task detail panel
- [ ] My Tasks dashboard (Developer view)
- [ ] QA Queue
- [ ] Implementation UAT Queue

### Phase 5: Advanced Features
- [ ] File upload system
- [ ] Comment system
- [ ] Task history tracking
- [ ] Email notifications
- [ ] Dashboard & reports
- [ ] Excel export

### Phase 6: Visual Design
- [ ] Implement design system (colors, typography, components)
- [ ] Light/dark mode toggle
- [ ] Mobile responsive layout
- [ ] Accessibility features

---

## Development Commands

### Database
```bash
# Run migrations
php artisan migrate

# Seed data
php artisan db:seed

# Reset and reseed
php artisan migrate:fresh --seed
```

### Models
All models are located in `app/Models/`:
- Site.php
- Project.php
- SiteProject.php
- Task.php
- TaskUat.php
- TargetDateRequest.php
- SlaRule.php
- Holiday.php
- TaskAssignee.php
- TaskComment.php
- TaskAttachment.php
- TaskHistory.php
- Notification.php

### Testing
```bash
# Run tests
php artisan test
```

---

## File Structure

```
project-tracker/
├── app/
│   ├── Models/          # Eloquent models
│   ├── Http/
│   │   ├── Controllers/ # Controllers (to be created)
│   │   └── Middleware/  # Role-based access (to be created)
├── database/
│   ├── migrations/      # 19 migration files (✓ Complete)
│   └── seeders/         # SlaRules & AdminUser seeders
├── resources/
│   ├── views/           # Blade templates (to be created)
│   └── css/             # Styles (to be created)
├── public/              # Web root
└── routes/
    └── web.php          # Routes (to be created)
```

---

## Important Notes

1. **Site vs Project Separation**: The system separates the **product** (e.g., HRIS) from **where it's deployed** (Site 1, Site 2). This means:
   - HRIS is defined once as a Project
   - Site 1 - HRIS and Site 2 - HRIS are separate Site Projects
   - Each has its own task list, lead, and team

2. **Target Date Control**: Only Project Leads can change target dates. Developers must submit a request which the Lead approves/rejects.

3. **Blocked Status**: When a task is blocked, it tracks:
   - The reason it's blocked
   - When it was blocked
   - Total blocked duration (for reporting)
   - SLA clock pauses

4. **Document Uploads**: Files can be attached to:
   - The original request/task
   - Comments
   - UAT results (e.g., QA failure screenshots)

5. **Task Numbers**: Auto-generated as `REQ-YYYY-####` (e.g., REQ-2026-0001)

---

## Technical Stack

- **Framework**: Laravel 8
- **PHP**: 7.3+
- **Database**: MySQL (XAMPP)
- **Frontend**: Blade Templates (Laravel default)
- **CSS Framework**: To be determined (Tailwind CSS or Bootstrap recommended)
- **JavaScript**: To be added (for Kanban drag & drop)

---

## Ready for Development

The database foundation is complete and ready for building the application interface. The next immediate step is to set up authentication and start building the admin interfaces.

**Estimated completion**: This was Phase 1-2 of a 10-phase project. Approximately 15-20% complete.

---

## Questions or Issues?

This foundation follows the detailed requirements in:
- `project-tracker-requirements (1).md`
- `project-tracker-checklist.md`

Refer to these documents for detailed specifications on each feature.
