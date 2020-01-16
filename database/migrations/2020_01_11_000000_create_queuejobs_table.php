<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQueuejobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('queuejobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('producerId');
            #$table->foreign('producerId')->references('id')->on('producer');
            $table->string('command');
            $table->enum('priority', ['HIGH', 'MEDIUM', 'LOW']);
            $table->string('processorId')->nullable();
            $table->enum('status', ['PENDING', 'DISPATCHED', 'PROCESSING', 'PROCESSED', 'FAILED']);
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
        Schema::dropIfExists('jobs');
    }
}
