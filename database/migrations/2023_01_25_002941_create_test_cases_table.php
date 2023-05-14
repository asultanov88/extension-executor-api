<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestCasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_cases', function (Blueprint $table) {
            $table->id('testCaseId')->index();
            $table->string('title', 500);
            $table->foreignId('projectId')->references('directoryId')->on('directories');
            $table->boolean('deleted')->default('0');
            $table->foreignId('createdBy')->references('id')->on('users');
            $table->foreignId('lastUpdatedBy')->references('id')->on('users');
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
        Schema::dropIfExists('test_cases');
    }
}
