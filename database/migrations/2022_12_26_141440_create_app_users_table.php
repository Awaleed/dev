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
        Schema::create('app_users', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable()->unique();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->unique();
            $table->string('country_code')->nullable();
            $table->decimal('commission', 8, 2)->nullable()->default(0.00);
            $table->timestamp('email_verified_at')->nullable();
            // $table->string('password');
            $table->foreignId('vendor_id')->nullable()->constrained();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_online')->default(false);
            $table->rememberToken();
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
        Schema::dropIfExists('app_users');
    }
};
