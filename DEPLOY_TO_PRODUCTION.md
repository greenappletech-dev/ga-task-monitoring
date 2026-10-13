# Deploy Task Failure Feature to Production

## 🔴 Error on Production:
```
500 Internal Server Error
POST http://gatsi-task-monitoring.greenappletechph.com/tasks/3/fail-qa
```

## Root Cause:
The new database columns for task failure tracking don't exist in production database yet.

## Required Database Columns:
- `parent_task_id` - Links rework tasks to original tasks
- `rejection_reason` - Stores why the task was failed
- `rejected_by` - User who failed the task
- `rejected_at` - When the task was failed

## ✅ Deployment Steps:

### Step 1: Connect to Production Server

```bash
ssh your-username@gatsi-task-monitoring.greenappletechph.com
# OR use your hosting provider's terminal/SSH access
```

### Step 2: Navigate to Project Directory

```bash
cd /path/to/your/laravel/project
# Common paths:
# /var/www/html/project-tracker
# /home/username/public_html/project-tracker
# /var/www/gatsi-task-monitoring
```

### Step 3: Check Current Migration Status

```bash
php artisan migrate:status
```

Look for these migrations:
- ✅ `2026_10_13_115436_add_related_task_to_tasks_table`
- ✅ `2026_10_13_120712_create_site_project_leads_table`

If they show as **"No"** under "Ran?" column, they need to be run.

### Step 4: Backup Database (IMPORTANT!)

```bash
# Create backup before running migrations
mysqldump -u your_db_user -p your_database_name > backup_$(date +%Y%m%d_%H%M%S).sql

# Example:
mysqldump -u gatsi_user -p gatsi_taskdb > backup_20261013.sql
```

### Step 5: Upload New Migration Files

You need to upload these migration files to production:

**File 1:** `database/migrations/2026_10_13_115436_add_related_task_to_tasks_table.php`
**File 2:** `database/migrations/2026_10_13_120712_create_site_project_leads_table.php`

Upload via:
- FTP/SFTP
- Git pull (if using Git)
- cPanel File Manager
- SCP command

```bash
# If using SCP from local machine:
scp database/migrations/2026_10_13_115436_add_related_task_to_tasks_table.php user@server:/path/to/project/database/migrations/
scp database/migrations/2026_10_13_120712_create_site_project_leads_table.php user@server:/path/to/project/database/migrations/
```

### Step 6: Upload Updated Controller

Upload the updated TaskController with fail QA/Implementation methods:

**File:** `app/Http/Controllers/TaskController.php`

```bash
scp app/Http/Controllers/TaskController.php user@server:/path/to/project/app/Http/Controllers/
```

### Step 7: Upload Updated Models

**Files:**
- `app/Models/Task.php`
- `app/Models/TaskHistory.php`
- `app/Models/TaskAttachment.php`

```bash
scp app/Models/Task.php user@server:/path/to/project/app/Models/
scp app/Models/TaskHistory.php user@server:/path/to/project/app/Models/
scp app/Models/TaskAttachment.php user@server:/path/to/project/app/Models/
```

### Step 8: Upload Updated Views

**File:** `resources/views/tasks/show.blade.php`

```bash
scp resources/views/tasks/show.blade.php user@server:/path/to/project/resources/views/tasks/
```

### Step 9: Upload Routes

**File:** `routes/web.php`

```bash
scp routes/web.php user@server:/path/to/project/routes/
```

### Step 10: Run Migrations on Production

```bash
# On production server
cd /path/to/project
php artisan migrate
```

You should see:
```
Migrating: 2026_10_13_115436_add_related_task_to_tasks_table
Migrated:  2026_10_13_115436_add_related_task_to_tasks_table
Migrating: 2026_10_13_120712_create_site_project_leads_table
Migrated:  2026_10_13_120712_create_site_project_leads_table
```

### Step 11: Clear Cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### Step 12: Create Storage Directory for Attachments

```bash
mkdir -p storage/app/task_attachments
chmod 775 storage/app/task_attachments
```

### Step 13: Test the Feature

1. Go to: http://gatsi-task-monitoring.greenappletechph.com/tasks/3
2. Click "Fail QA Testing" button
3. Enter rejection reason
4. Submit
5. ✅ Should create rework task successfully!

## 🔧 Alternative: Using Git (Recommended)

If your production is using Git:

```bash
# On production server
cd /path/to/project

# Pull latest changes
git pull origin main  # or master

# Run migrations
php artisan migrate

# Clear cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Set permissions
chmod -R 775 storage bootstrap/cache
```

## ⚠️ Important Notes:

1. **Always backup database before running migrations**
2. **Test on staging environment first** if available
3. **Run migrations during low-traffic hours** if possible
4. **Check .env file** on production has correct database credentials
5. **Verify file permissions** after upload (usually 644 for files, 755 for directories)

## 📝 Files That Need to Be on Production:

### Migrations:
- [x] `database/migrations/2026_10_13_115436_add_related_task_to_tasks_table.php`
- [x] `database/migrations/2026_10_13_120712_create_site_project_leads_table.php`

### Controllers:
- [x] `app/Http/Controllers/TaskController.php` (updated with failQA and failImplementation methods)

### Models:
- [x] `app/Models/Task.php` (uncommented history and attachments relationships)
- [x] `app/Models/TaskHistory.php`
- [x] `app/Models/TaskAttachment.php`

### Routes:
- [x] `routes/web.php` (added fail-qa and fail-implementation routes)

### Views:
- [x] `resources/views/tasks/show.blade.php` (added fail buttons and modals)

## 🧪 Quick Test Command on Production:

```bash
# SSH into production
cd /path/to/project

# Check if columns exist
php artisan tinker
>>> Schema::hasColumn('tasks', 'parent_task_id')
>>> Schema::hasColumn('tasks', 'rejection_reason')
>>> Schema::hasColumn('tasks', 'rejected_by')
>>> Schema::hasColumn('tasks', 'rejected_at')
>>> exit
```

All should return `true`. If any return `false`, the migration needs to be run.

## 🆘 If Migration Fails:

Check the error message. Common issues:

**"Table 'tasks' doesn't exist"**
- The tasks table doesn't exist. Check database connection.

**"Duplicate column name 'parent_task_id'"**
- Migration already ran. Skip it with `--pretend` flag.

**"SQLSTATE[42S22]: Column not found"**
- Previous migrations incomplete. Check migration history.

## ✅ Verification Checklist:

After deployment, verify:

- [ ] Migration ran successfully (check with `php artisan migrate:status`)
- [ ] New columns exist in database
- [ ] Fail QA button appears on tasks in "For QA UAT" status
- [ ] Fail Implementation button appears on tasks in "For Implementation UAT" status
- [ ] Clicking button shows modal
- [ ] Submitting modal creates rework task
- [ ] Original task status changes to "Blocked"
- [ ] Rework task has `[REWORK]` prefix
- [ ] Related tasks card shows parent-child relationship
- [ ] Movement history shows failure with reason

## 📞 Need Help?

If you encounter errors:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Check server error logs: `/var/log/apache2/error.log` or `/var/log/nginx/error.log`
3. Enable debug mode temporarily: Set `APP_DEBUG=true` in `.env` (remember to turn off after!)
