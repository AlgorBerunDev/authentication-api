<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('identity')->unique();
            $table->string('identitytype', 100)->nullable();
            $table->string('username');
            $table->timestamp('verified_at')->nullable();
            $table->string('password');
            $table->boolean('is_blocked')->default(false);

            // userga ortiqcha devicelar ulanishini oldini olish uchun sessiyalar soni biriktirilinadi userning o'zidan
            // ammo super_session_max_count ham mavjud u tizim administratori orqali biriktiriliniladi
            $table->integer('session_max_count')->default(config('session.max_count')); // min 1
            $table->integer('super_session_max_count')->default(config('session.super_max_count'));

            // agar aynan shu userga logining qilish kerakli limitdan oshsa, belgilangan vaqtgacha bloklab qo'yiladi
            // (faqat logining uchun)
            $table->timestamp('confirmation_blocked_to')->useCurrent();
            $table->timestamp('login_blocked_to')->useCurrent();
            $table->integer('max_account_create')->default(10);
            $table->rememberToken()->nullable();
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
        Schema::dropIfExists('users');
    }
}
