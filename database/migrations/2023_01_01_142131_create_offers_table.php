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
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('promotional_text')->nullable();
            $table->text('url')->nullable();
            $table->string('type')->nullable();
            $table->double('delivery_fee', 15, 2)->nullable();
            $table->boolean('delivery')->default(false);
            $table->boolean('is_active')->default(true);

            $table->timestamp('starting_at')->default(now());
            $table->timestamp('ending_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('offer_product', function (Blueprint $table) {
            $table->unsignedBigInteger('offer_id')->index();
            $table->foreign('offer_id')->references('id')->on('offers')->onDelete('cascade');
            $table->unsignedBigInteger('product_id')->index();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->primary(['offer_id', 'product_id']);
        });

        Schema::create('offer_vendor', function (Blueprint $table) {
            $table->unsignedBigInteger('offer_id')->index();
            $table->foreign('offer_id')->references('id')->on('offers')->onDelete('cascade');
            $table->unsignedBigInteger('vendor_id')->index();
            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
            $table->primary(['offer_id', 'vendor_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('offer_product');
        Schema::dropIfExists('offer_vendor');
        Schema::dropIfExists('offers');
    }
};
