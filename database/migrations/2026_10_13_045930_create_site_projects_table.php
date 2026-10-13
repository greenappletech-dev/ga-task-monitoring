<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSiteProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('lead_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('version')->nullable();
            $table->date('go_live_date')->nullable();
            $table->enum('status', ['Development', 'Live', 'Maintenance'])->default('Development');
            $table->timestamps();

            // Ensure same project can't be added to same site twice
            $table->unique(['site_id', 'project_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('site_projects');
    }
}
