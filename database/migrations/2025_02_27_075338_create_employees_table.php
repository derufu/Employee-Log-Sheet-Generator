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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->string('suffix')->nullable();
            $table->string('extension_name')->nullable();
            $table->string('address')->nullable();
            $table->date('birthdate');
            $table->string('employee_id')->unique();
            $table->string('email')->unique();
            $table->string('contact_number');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->string('position_type');
            $table->string('position');
            $table->enum('status', ['active', 'inactive']);
            $table->string('emergency_contact_name');
            $table->string('emergency_contact_number');
            $table->string('emergency_address');
            $table->string('image')->nullable(); // Add this line for the image column
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
