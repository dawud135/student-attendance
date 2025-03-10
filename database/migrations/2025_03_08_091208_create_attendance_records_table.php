<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Student
            $table->foreignId('teacher_id')->constrained('users')->onDelete('restrict'); // Teacher
            $table->foreignId('school_class_id')->constrained('school_classes')->onDelete('restrict');
            $table->foreignId('school_subject_id')->constrained('school_subjects')->onDelete('restrict');
            $table->string('grade'); // Grade at the time of attendance
            $table->date('date');
            $table->enum('status', ['on-time', 'late', 'absent'])->default('on-time');
            $table->integer('minutes_late')->default(0);
            $table->text('reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('attendance_records');
    }
};