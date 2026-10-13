<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskUatTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_uat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->enum('stage', ['QA', 'Implementation']); // Two-stage UAT
            $table->enum('result', ['Pass', 'Fail']);
            $table->text('notes')->nullable();
            $table->foreignId('tested_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('tested_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('task_uat');
    }
}
