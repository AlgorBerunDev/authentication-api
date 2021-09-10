<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_id')->unique();
            $table->string('monday_begin', 5);
            $table->string('monday_end', 5);
            $table->string('tuesday_begin', 5);
            $table->string('tuesday_end', 5);
            $table->string('wednesday_begin', 5);
            $table->string('wednesday_end', 5);
            $table->string('thursday_begin', 5);
            $table->string('thursday_end', 5);
            $table->string('friday_begin', 5);
            $table->string('friday_end', 5);
            $table->string('saturday_begin', 5);
            $table->string('saturday_end', 5);
            $table->string('sunday_begin', 5);
            $table->string('sunday_end', 5);
            $table->string('lunch_time_begin', 5);
            $table->string('lunch_time_end', 5);
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schedules');
    }
}
