<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Linked to users
            $table->string('nis', 20)->unique(); // Student ID
            $table->unsignedTinyInteger('grade'); // Academic grade
            $table->unsignedSmallInteger('reward_points')->default(0); // Reward system
            $table->unsignedSmallInteger('violation_points')->default(0); // Discipline tracking
            $table->timestamps();
            $table->softDeletes(); // Soft delete support
        });
    }

    public function down(): void {
        Schema::dropIfExists('students');
    }
};
