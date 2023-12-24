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
        Schema::create('system_earning_transactions', function (Blueprint $table) {
            $table->id();

            $table->double('amount', 10, 2);
            $table->foreignId('order_id')->constrained();
            $table->foreignId('vendor_transaction_id')->constrained();

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
        Schema::dropIfExists('system_earning_transactions');
    }
};
