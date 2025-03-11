<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $user = User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('admin'),
        ]);
        $role = Role::create(['name' => 'Admin']);
        
        $perm1 = Permission::create(['name' => 'Viewing']);
        $perm2 = Permission::create(['name' => 'Creation']);
        $perm3 = Permission::create(['name' => 'Edition']);
        $perm4 = Permission::create(['name' => 'Deletion']);
        $role->syncPermissions($perm1);
        $role->syncPermissions($perm2);
        $role->syncPermissions($perm3);
        $role->syncPermissions($perm4);

        $user->assignRole($role);
    }
}
