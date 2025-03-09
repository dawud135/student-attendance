<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SchoolSubject;
use Illuminate\Support\Str;

class SubjectSeeder extends Seeder {
    public function run(): void {
        $subjects = [
            ['name' => 'Mathematics', 'code' => 'MATH'],
            ['name' => 'Science', 'code' => 'SCI'],
            ['name' => 'History', 'code' => 'HIST'],
            ['name' => 'Nahwu', 'code' => 'NAH'],
            ['name' => 'Shorof', 'code' => 'SHOR'],
            ['name' => 'Aqidah Akhlak', 'code' => 'AA'],
            ['name' => 'Qur\'an', 'code' => 'QUR'],
            ['name' => 'English Language', 'code' => 'ENG'],
            ['name' => 'English Literature', 'code' => 'ENGLIT'],
            ['name' => 'Bahasa Indonesia', 'code' => 'BI'],
            ['name' => 'Bahasa Indonesia Literature', 'code' => 'BILIT'],
        ];

        foreach ($subjects as $subject) {
            SchoolSubject::firstOrCreate(
                ['code' => $subject['code']], // Ensure uniqueness by 'code'
                ['name' => $subject['name']]
            );
        }

        echo "✅ Subjects Seeded.\n";
    }
}
