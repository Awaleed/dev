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
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->double('amount', 10, 2);
            $table->string('ref')->nullable();
            $table->string('reason')->nullable();
            $table->string('session_id')->nullable();
            $table->foreignId('wallet_id')->constrained();
            $table->string('payment_method_id')->nullable();
            $table->string('status')->default('pending'); //, ['pending', 'failed', 'successful']
            $table->boolean('is_credit')->default(false);
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
        Schema::dropIfExists('wallet_transactions');
    }
};
