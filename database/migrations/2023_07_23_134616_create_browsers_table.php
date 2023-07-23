<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrowsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('browser', function (Blueprint $table) {
            $table->id('browserId')->index();
            $table->string('name', 100);
            $table->boolean('deleted')->default('0');
            $table->integer('replacedByBrowserId')->nullable()->references('browserId')->on('browser')->onDelete('cascade');
            $table->foreignId('createdBy')->references('id')->on('users');
            $table->foreignId('updatedBy')->references('id')->on('users');
        });   
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('browsers');
    }
}
