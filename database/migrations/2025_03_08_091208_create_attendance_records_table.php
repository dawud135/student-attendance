<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Student;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

return new class extends Migration {
    public function up(): void {
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Student
            $table->foreignId('teacher_id')->constrained('users')->onDelete('restrict'); // Teacher
            $table->foreignId('school_class_id')->constrained('school_classes')->onDelete('restrict');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('restrict'); // Subject
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