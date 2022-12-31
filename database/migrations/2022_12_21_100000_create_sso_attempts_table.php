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
    public function up(): void
    {
        Schema::create('sso_attempts', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->uuid('state');
            $table->string('url', 2000);
            $table->ipAddress();
            $table->string('result_code', 300)->nullable();
            $table->string('username', 100)->nullable();
            $table->string('identify_code', 100)->nullable();
            $table->string('type', 10);
            $table->boolean('is_successful')->nullable();
            $table->timestamps();
            $table->dateTime('confirmed_at')->nullable();
            $table->dateTime('expired_at')->nullable();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('sso_attempts');
    }
};
