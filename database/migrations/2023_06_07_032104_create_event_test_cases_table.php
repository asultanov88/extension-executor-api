<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventTestCasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_test_cases', function (Blueprint $table) {
            $table->id('eventTestCaseId')->index();
            $table->foreignId('eventId')->references('eventId')->on('events');
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
        Schema::dropIfExists('event_test_cases');
    }
}
