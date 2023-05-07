<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDirectoryTestCasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('directory_test_cases', function (Blueprint $table) {
            $table->id('directoryTestCaseId')->index();
            $table->foreignId('directoryId')->references('directoryId')->on('directories');
            $table->foreignId('testCaseId')->references('testCaseId')->on('test_cases');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('directory_test_cases');
    }
}
