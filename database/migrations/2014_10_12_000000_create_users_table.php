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
            // $table->string('organization_logo')->nullable();
            $table->string('organization_logo_url')->nullable();
            $table->string('organization_logo_public_id')->nullable();
            $table->string('fullname');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('phone');
            $table->string('country');
            $table->string('city')->nullable();
            $table->string('company')->nullable();
            $table->string('job_function');
            $table->string('job_role')->nullable();
            $table->string('password');
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
