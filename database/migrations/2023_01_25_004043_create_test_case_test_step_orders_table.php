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
            $table->foreignId('TestCaseId')->references('TestCaseId')->on('test_cases');
            $table->foreignId('TestStepId')->references('TestStepId')->on('test_steps');
            $table->integer('Order');
            // TestCaseId and Order has a unique constraint.
            $table->unique(['TestCaseId', 'Order']); 
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
