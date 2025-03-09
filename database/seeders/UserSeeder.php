<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Student;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder {
    public function run(): void {
        // Ensure roles exist
        $roles = ['admin', 'teacher', 'student'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        $studentRole = Role::firstOrCreate(['name' => 'student']);

        // Create Admin (if not exists)
        if (!User::where('email', 'daud@somewhere.com')->exists()) {
            $admin = User::create([
                'name' => 'Admin User',
                'email' => 'daud@somewhere.com',
                'password' => Hash::make('rahasia123'),
            ]);
            $admin->assignRole('admin');
            echo "✅ Admin user created.\n";
        } else {
            echo "⚠️ Admin user already exists.\n";
        }

        // Create Teacher (if not exists)
        if (!User::where('email', 'teacher@example.com')->exists()) {
            $teacher = User::create([
                'name' => 'Fulan LC.',
                'email' => 'teacher@example.com',
                'password' => Hash::make('password'),
            ]);
            $teacher->assignRole('teacher');
            echo "✅ Teacher user created.\n";
        } else {
            echo "⚠️ Teacher user already exists.\n";
        }

        // Create Fake Students (if not exists)
        $existingStudents = User::whereHas('roles', fn($q) => $q->where('name', 'student'))->count();
        $studentsToCreate = max(0, 10 - $existingStudents); // Ensure we always have 10 students

        if ($studentsToCreate > 0) {
            User::factory($studentsToCreate)->create()->each(function ($user) use ($studentRole) {
                $user->assignRole('student');
                
                // Create a student record linked to the user
                Student::create([
                    'user_id' => $user->id,
                    'nis' => 'NIS' . $user->id,
                    'grade' => rand(1, 12),
                    'reward_points' => rand(0, 100),
                    'violation_points' => rand(0, 50),
                ]);
            });

            echo "✅ $studentsToCreate new students created.\n";
        } else {
            echo "⚠️ Students already exist. No new students were created.\n";
        }
    }
}
