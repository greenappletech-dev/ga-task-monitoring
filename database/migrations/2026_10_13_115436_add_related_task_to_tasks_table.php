<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelatedTaskToTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Parent/related task ID - for tracking rework tasks
            $table->unsignedBigInteger('parent_task_id')->nullable()->after('id');
            $table->foreign('parent_task_id')->references('id')->on('tasks')->onDelete('set null');

            // Rejection/failure reason
            $table->text('rejection_reason')->nullable()->after('description');

            // Rejected by user
            $table->unsignedBigInteger('rejected_by')->nullable()->after('qa_assigned_to');
            $table->foreign('rejected_by')->references('id')->on('users')->onDelete('set null');

            // Rejection date
            $table->timestamp('rejected_at')->nullable()->after('date_assigned');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['parent_task_id']);
            $table->dropForeign(['rejected_by']);
            $table->dropColumn(['parent_task_id', 'rejection_reason', 'rejected_by', 'rejected_at']);
        });
    }
}
