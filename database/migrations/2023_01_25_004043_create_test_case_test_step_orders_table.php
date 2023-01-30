<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestCaseTestStepOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_case_test_step_orders', function (Blueprint $table) {
            $table->foreignId('testCaseId')->references('testCaseId')->on('test_cases');
            $table->foreignId('testStepId')->references('testStepId')->on('test_steps');
            $table->integer('order');
            // TestCaseId and Order has a unique constraint.
            $table->unique(['testCaseId', 'order']); 
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
        Schema::dropIfExists('test_case_test_step_orders');
    }
}
