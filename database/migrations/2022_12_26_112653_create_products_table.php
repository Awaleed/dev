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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->string('sku')->nullable();
            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->double('price', 15, 2);
            $table->double('discount_price', 15, 2)->default(0);
            $table->string('capacity')->default("1")->nullable();
            $table->string('unit')->default("kg");
            $table->string('package_count')->nullable();
            $table->integer('available_qty')->nullable();
            $table->integer('preparation_time')->nullable();
            $table->boolean('featured')->default(false);
            $table->boolean('deliverable')->default(true);
            $table->boolean('is_active')->default(true);
            $table->boolean('with_option')->default(true);
            $table->foreignId('vendor_id')->constrained()->references('id')->on('vendors');
            $table->foreignId('category_id')->nullable()->constrained()->references('id')->on('categories');
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
        Schema::dropIfExists('products');
    }
};
