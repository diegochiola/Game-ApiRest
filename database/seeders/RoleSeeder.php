<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Role::create(['name' => 'admin', 'guard_name' => 'api']);
        $player = Role::create(['name' => 'player', 'guard_name' => 'api']);

        /*
        // Define permissions for roles in the 'api' guard
        Permission::create(['name' => 'register'])->syncRoles([$admin, $player]);
        Permission::create(['name' => 'login'])->syncRoles([$admin, $player]);
        Permission::create(['name' => 'getGames'])->syncRoles([$player]);
        Permission::create(['name' => 'createGame'])->syncRoles([$player]);
        Permission::create(['name' => 'destroy'])->syncRoles([$player]);
        Permission::create(['name' => 'percentageOfWins'])->syncRoles([$admin]);
        Permission::create(['name' => 'allUsersPercentageOfWins'])->syncRoles([$admin]);
        Permission::create(['name' => 'getTotalPercentageOfWins'])->syncRoles([$admin]);
        */
    }
}
