<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AttendanceRecord;
use App\Models\User;
use App\Models\SchoolClass;
use App\Models\SchoolSubject;
use Faker\Factory as Faker;

class AttendanceRecordSeeder extends Seeder {
    public function run(): void {
        $faker = Faker::create();

        // Fetch students, teachers, school classes, and subjects
        $userStudents = User::role('student')->get();
        $userTeachers = User::role('teacher')->get();
        $classes = SchoolClass::all();
        $subjects = SchoolSubject::all();

        // Check if we have enough data to seed attendance records
        if ($userStudents->isEmpty() || $userTeachers->isEmpty() || $classes->isEmpty() || $subjects->isEmpty()) {
            echo "⚠️ Not enough data to seed attendance records.\n";
            return;
        }

        // Generate 100 random attendance records
        for ($i = 0; $i < 100; $i++) {
            $userStudent = $userStudents->random();
            $teacher = $userTeachers->random();
            $class = $classes->random();
            $subject = $subjects->random();

            // Random attendance status
            $status = $faker->randomElement(['on-time', 'late', 'absent']);
            $minutesLate = ($status === 'late') ? $faker->numberBetween(1, 30) : 0;
            $reason = ($status !== 'on-time') ? $faker->sentence() : null;

            if($userStudent->student == null) {
                dd($userStudent);
            }

            AttendanceRecord::create([
                'user_id' => $userStudent->id,
                'teacher_id' => $teacher->id,
                'school_class_id' => $class->id,
                'school_subject_id' => $subject->id,
                'grade' => $userStudent->student->grade,
                'date' => $faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
                'status' => $status,
                'minutes_late' => $minutesLate,
                'reason' => $reason,
            ]);
        }

        echo "✅ 100 Attendance Records Seeded Successfully.\n";
    }
}
