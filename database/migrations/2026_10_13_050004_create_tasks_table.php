<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('task_no')->unique(); // REQ-YYYY-####
            $table->foreignId('site_project_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['New Feature', 'Enhancement', 'Bug', 'Data Fix', 'Support', 'Report']);
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('source', ['Implementation Request', 'Internal Development'])->default('Implementation Request');
            $table->enum('priority', ['Urgent', 'High', 'Medium', 'Low'])->default('Medium');
            $table->enum('status', ['New', 'For Clarification', 'Approved', 'Assigned', 'In Progress', 'Blocked', 'For QA UAT', 'For Implementation UAT', 'Done', 'Rejected', 'Cancelled', 'On Hold'])->default('New');

            // Request tracking
            $table->foreignId('requested_by')->constrained('users')->onDelete('cascade');
            $table->date('date_requested');
            $table->string('client_contact')->nullable();

            // Assignment
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('qa_assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->date('date_assigned')->nullable();
            $table->date('target_date')->nullable();
            $table->date('sla_due_date')->nullable();

            // Progress tracking
            $table->text('blocked_reason')->nullable();
            $table->timestamp('blocked_at')->nullable();
            $table->integer('blocked_duration_hours')->default(0); // Track total blocked time
            $table->date('completed_date')->nullable();
            $table->integer('percent_complete')->default(0);
            $table->decimal('est_effort', 8, 2)->nullable(); // hours or days
            $table->decimal('actual_effort', 8, 2)->nullable();
            $table->integer('return_count')->default(0); // Times returned from UAT

            $table->timestamps();

            $table->index('status');
            $table->index('priority');
            $table->index('target_date');
            $table->index('assigned_to');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
