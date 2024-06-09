<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('fullname');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phone');
            $table->string('job');
            $table->string('job_other')->nullable();
            $table->string('country');
            $table->string('city')->nullable();
            $table->string('job_role')->nullable();
            $table->string('job_role_other')->nullable();
            $table->string('company')->nullable();
            $table->string('organization_logo')->nullable();
            $table->boolean('status')->default(1);
            // $table->foreignId('role_id')->constrained('roles')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
