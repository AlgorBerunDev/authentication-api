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
            $table->string('identitytype', 100)->default('not defined');
            $table->string('username');
            $table->timestamp('verified_at')->nullable();
            $table->string('password');
            $table->boolean('is_blocked')->default(false);

            // Ortiqcha verify codeni yubormaslik uchun yuborilgan verify codelar sonini xisoblash uchun kerak
            // Agar kerakli limitdan oshib ketsa login_is_blocked_to kerakli vaqtgacha bloklanadi
            $table->integer('verify_code_sended_count')->default(0);
            $table->timestamp('verify_code_sended_at')->nullable();

            // userga ortiqcha devicelar ulanishini oldini olish uchun sessiyalar soni biriktirilinadi userning o'zidan
            // ammo super_session_max_count ham mavjud u tizim administratori orqali biriktiriliniladi
            $table->integer('session_max_count')->default(10); // min 1
            $table->integer('super_session_max_count')->default(100);

            // agar aynan shu userga logining qilish kerakli limitdan oshsa, belgilangan vaqtgacha bloklab qo'yiladi
            // (faqat logining uchun)
            $table->timestamp('login_is_blocked_to')->nullable();
            // xar safar login_is_blocked_to qiymatini berishda u qancha vaqt oralig'ida blokga tushganligiga qarab yana qancha vaqtga bloklash
            // kerak bolgan vaqtni xisoblash uchun kerak
            $table->integer('login_blocked_count')->default(0);
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
