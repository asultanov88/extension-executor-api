<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestCaseExecutionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_case_executions', function (Blueprint $table) {
            $table->id('testCaseExecutionId')->index();
            $table->foreignId('testCaseId')->references('testCaseId')->on('test_cases');
            $table->foreignId('statusId')->references('statusId')->on('statuses');
            $table->foreignId('resultId')->nullable()->references('resultId')->on('results');
            $table->foreignId('executedBy')->references('id')->on('users');
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
        Schema::dropIfExists('test_case_executions');
    }
}
