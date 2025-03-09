<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder {
    public function run(): void {
        // Ensure roles exist
        $roles = ['student', 'teacher', 'admin'];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        echo "✅ Roles ensured: student, teacher, admin\n";

        // Ensure permissions exist
        $permissions = ['mark attendance', 'view reports'];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
        }

        echo "✅ Permissions ensured: mark attendance, view reports\n";

        // Assign permissions (only if role exists)
        if ($teacherRole = Role::where('name', 'teacher')->first()) {
            $teacherRole->givePermissionTo('mark attendance');
        }

        if ($adminRole = Role::where('name', 'admin')->first()) {
            $adminRole->givePermissionTo('view reports');
        }

        echo "✅ Permissions assigned to roles\n";
    }
}