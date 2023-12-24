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
        Schema::create('order_products', function (Blueprint $table) {
            $table->id();
            $table->integer('quantity')->default(1);
            $table->double('price', 8, 2)->default(0);
            $table->string('options_ar')->nullable();
            $table->string('options_en')->nullable();
            $table->string('options_ids')->nullable();
            $table->foreignId('order_id')->constrained();
            $table->foreignId('product_id')->constrained();
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
        Schema::dropIfExists('order_products');
    }
};
