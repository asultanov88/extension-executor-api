<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImportedTestCasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('imported_test_cases', function (Blueprint $table) {
            $table->foreignId('testCaseId')->references('testCaseId')->on('test_cases');
            $table->foreignId('importedTestCaseId')->references('testCaseId')->on('test_cases');
            $table->integer('importOrder')->foreign('importOrder')->references('order')->on('test_case_test_step_orders');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('imported_test_cases');
    }
}
