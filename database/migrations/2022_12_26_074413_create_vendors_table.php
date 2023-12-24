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
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->text('description')->nullable();
            $table->double('base_delivery_fee', 15, 2)->default(0);
            $table->double('delivery_fee', 15, 2)->default(0);
            $table->double('delivery_range', 8, 2)->default(0);
            $table->string('tax')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->double('commission', 8, 2)->default(0);
            $table->boolean('pickup')->default(true);
            $table->boolean('delivery')->default(false);
            $table->string('delivery_time')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('charge_per_km')->default(false);
            $table->boolean('is_open')->default(true);
            $table->boolean('auto_assignment')->default(true);
            $table->boolean('auto_accept')->default(false);
            $table->boolean('allow_schedule_order')->default(false);
            $table->boolean('has_sub_categories')->default(false);
            $table->double('min_order', 15, 2)->nullable();
            $table->double('max_order', 15, 2)->nullable();
            $table->boolean('use_subscription')->default(false);
            $table->boolean('show_location')->default(true);
            $table->boolean('can_message_before_order')->default(true);
            $table->string('approval_status')->default('pending');
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
        Schema::dropIfExists('vendors');
    }
};
