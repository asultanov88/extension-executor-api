<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestStepExecutionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_step_executions', function (Blueprint $table) {
            $table->id('testStepExecutionId')->index();
            $table->foreignId('testCaseExecutionId')->references('testCaseExecutionId')->on('test_case_executions');
            $table->foreignId('testStepId')->references('testStepId')->on('test_steps');
            $table->foreignId('resultId')->nullable()->references('resultId')->on('results');
            $table->integer('sequence');
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
        Schema::dropIfExists('test_step_executions');
    }
}
