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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('number')->nullable()->unique();
            $table->string('code');
            $table->string('verification_code')->nullable();
            $table->string('note')->nullable();
            $table->string('reason')->nullable();
            $table->string('payment_status')->default('pending'); // , ['pending', 'review', 'failed', 'cancelled', 'successful']
            $table->double('sub_total', 15, 2)->default(0);
            $table->double('tip', 15, 2)->default(0);
            $table->double('discount', 15, 2)->default(0);
            $table->double('delivery_fee', 15, 2)->default(0);
            $table->double('commission', 15, 2)->default(0);
            $table->double('tax', 15, 2)->default(0);
            $table->double('total', 15, 2)->default(0);

            $table->string('delivery_note')->nullable();
            $table->double('latitude');
            $table->double('longitude');
            $table->string('address')->nullable();

            $table->double('vendor_latitude');
            $table->double('vendor_longitude');
            $table->string('vendor_address')->nullable();
            $table->text('polyline')->nullable();
            $table->double('distance')->nullable();

            $table->date('pickup_date')->nullable();
            $table->time('pickup_time')->nullable();
            $table->double('weight', 10, 2)->default(0);
            $table->double('width', 10, 2)->default(0);
            $table->double('length', 10, 2)->default(0);
            $table->double('height', 10, 2)->default(0);
            //end package delivery columns


            $table->foreignId('payment_method_id')->constrained();
            $table->foreignId('vendor_id')->constrained();
            $table->uuid('user_id');
            $table->uuid('driver_id')->nullable();
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
        Schema::dropIfExists('orders');
    }
};
