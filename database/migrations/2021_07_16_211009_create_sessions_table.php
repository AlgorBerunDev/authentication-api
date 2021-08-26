<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSessionsTable extends Migration
{
    /**
     * Bu sessions xar bir device uchun bitta bo'ladi va userga biriktirilinadi.
     * 2chi va undan ortiq devicelar tizimga kirib bo'lgan devicelarga yuborilgan verify_code orqali tizimga kirishi mumkin
     * aks holda identity(telefon raqam yoki email manzil) ma'lumotiga asoslangan verify_code kerakli manzilga yuboriliniladi
     * verify_code tasdiqlanmaguncha sessiya aktiv xolatni qabul qilmaydi va user nomidan amallar bajarish xuquqiga ega bo'lmaydi
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sessions', function (Blueprint $table) {
            $table->id();
            /**
             * secretKey va refreshKeyni qo'shilganliligini sababi kerakli klyuchlar hacker tomonidan aniqlangan taqdirda ham
             * faqatgin bitta user ma'lumotiga dostupni beradi
             * Agar bitta userning ma'lumotini ham xavfsizligini oshirmoqchi bo'lsak unda token yuboruvchining device va
             * location ma'lumotlarini tekshirishimiz kerak, ammo bunga xojat yo'q sababi accessTokenning yashash davrini kamaytirsak ya'ni xakker kerakli
             * kalitni topish uchun tokenni brut qilish uchun ketgan vaqtdan kamroq vaqt qo'yilsa unda xakker eski kalitga ega bo'ladi va u keraksiz bo'lib qoladi.
             * ya'ni ungacha yangi secretKey va refreshKeylar xosil qilinib bo'liniladi.
             *
             */
            $table->string('secretKey', 512);
            $table->string('refreshKey', 512);
            $table->text('refreshToken')->nullable();
            $table->text('location')->nullable();
            $table->text('device')->nullable();
            $table->text('fcm_token')->nullable();
            $table->foreignId('user_id')
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->timestamp('confirmation_blocked_to')->useCurrent();
            $table->boolean('is_activated')->default(false);
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
        Schema::dropIfExists('sessions');
    }
}
