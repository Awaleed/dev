<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('app_users');
            $table->string('title_ar');
            $table->string('title_en');
            $table->string('url')->nullable();
            $table->string('text_ar');
            $table->string('text_en');
            $table->timestamp('read_at')->nullable();
            $table->foreignId('notifiable_id')->nullable();
            $table->string('notifiable_type')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('app_notifications');
    }
};
