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
        Schema::create('technical_supports', function (Blueprint $table) {
            $table->id();
            $table->string('type'); //, ['error', 'complain']
            $table->text('title');
            $table->text('body');
            $table->foreignId('user_id')->constrained()->references('id')->on('app_users');

            $table->foreignId('admin_id')->nullable()->constrained()->references('id')->on('users');
            $table->string('status')->nullable();
            $table->text('replay')->nullable();

            $table->timestamps();
            // $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('technical_supports');
    }
};
