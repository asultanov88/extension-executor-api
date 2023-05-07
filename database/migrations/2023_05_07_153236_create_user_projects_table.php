<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_projects', function (Blueprint $table) {
            $table->id('userProjectId')->index();
            $table->foreignId('userProfileId')->references('id')->on('users');
            $table->foreignId('projectId')->references('directoryId')->on('directories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_projects');
    }
}
