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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_en');
            $table->string('slug')->nullable();
            $table->text('instruction')->nullable();
            $table->string('secret_key')->nullable();
            $table->string('public_key')->nullable();
            $table->string('hash_key')->nullable();
            $table->string('class')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_cash')->default(false);
            $table->boolean('use_taxi')->default(true);
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
        Schema::dropIfExists('payment_methods');
    }
};
