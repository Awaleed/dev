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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->double('amount', 10, 2);
            $table->string('ref')->nullable();
            $table->string('session_id')->nullable();
            $table->foreignId('order_id')->constrained();
            $table->string('status')->default('pending'); //, ['pending', 'failed', 'review', 'successful']
            $table->string('type'); //, ['moyaser', 'cod']
            $table->json('raw_object')->nullable();
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
        Schema::dropIfExists('payments');
    }
};
