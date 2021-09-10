<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamp('founded_date');
            $table->string('email')->unique();
            $table->string('website')->unique();
            $table->integer('amount_of_workers');
            $table->string('country');
            $table->string('city');
            $table->string('logo');
            $table->boolean('publish')->default(false);
            $table->timestamp('published_at')->useCurrent();
            $table->integer('max_product_uploads')->default(100);
            $table->double('balance', 8, 2)->default(0.0);
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
        Schema::dropIfExists('accounts');
    }
}
