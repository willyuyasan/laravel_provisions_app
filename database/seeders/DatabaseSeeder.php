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
        $permission = Permission::create(['name' => ['Creation','Edition','Deletion','Viewing']]);
        $role->assignRole($permission);
        $user->assignRole($role);
    }
}
