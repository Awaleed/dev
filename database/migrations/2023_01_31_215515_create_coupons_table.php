<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->text('description')->nullable();
            $table->double('discount', 8, 2)->default(0);
            $table->double('maximum_discount', 8, 2)->nullable();
            $table->boolean('percentage')->default(true);
            $table->date('expires_on')->default(now());
            $table->integer('times')->nullable();
            $table->integer('times_per_user')->nullable();
            $table->boolean('exclude_discounted')->default(false);
            $table->boolean('free_delivery')->default(false);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
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
        Schema::dropIfExists('coupons');
    }
}
