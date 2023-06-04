<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestStepExecutionScreenshotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_step_execution_screenshots', function (Blueprint $table) {
            $table->foreignId('testStepExecutionId')->references('testStepExecutionId')->on('test_step_executions');
            $table->foreignId('screenshotId')->references('screenshotId')->on('screenshots');
            $table->longtext('screenshotUuid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('test_step_execution_screenshots');
    }
}
