<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SchoolClass;

class SchoolClassSeeder extends Seeder {
    public function run(): void {
        $classes = [
            ['name' => 'Class 7A'],
            ['name' => 'Class 7B'],
            ['name' => 'Class 8A'],
            ['name' => 'Class 8B'],
            ['name' => 'Class 9A'],
            ['name' => 'Class 9B'],
            ['name' => 'Class 10A'],
            ['name' => 'Class 10B'],
            ['name' => 'Language Laboratory'],
            ['name' => 'Computer Laboratory'],
            ['name' => 'Chemistry Laboratory'],
            
        ];

        foreach ($classes as $class) {
            SchoolClass::firstOrCreate($class);
        }

        echo "✅ School Classes Seeded.\n";
    }
}
