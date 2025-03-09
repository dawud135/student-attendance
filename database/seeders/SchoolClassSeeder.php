<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SchoolClass;

class SchoolClassSeeder extends Seeder {
    public function run(): void {
        $classes = [
            ['name' => 'Class 11A'],
            ['name' => 'Class 11B'],
            ['name' => 'Class 12A'],
            ['name' => 'Class 12B'],
            ['name' => 'Class 13A'],
            ['name' => 'Class 13B'],
        ];

        foreach ($classes as $class) {
            SchoolClass::firstOrCreate($class);
        }

        echo "✅ School Classes Seeded.\n";
    }
}
