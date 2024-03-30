<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //primero corroborar que funcione
        User::create([         
            'name' => 'admin',
            'nickname' => 'admin',
            'email' => 'admin@admin.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
        ]);

        User::factory()
            ->count(10)
            ->hasGames(5)
            ->create();
        /*
        //logica de asignacion de roles para poblar tablas
        $role1 = Role::firstOrCreate(
            ['name' => 'admin', 'guard_name' => 'web']
        );

        $role2 = Role::firstOrCreate(
            ['name' => 'player', 'guard_name' => 'web']
        );

        $user1 = User::firstOrCreate(
            [
                'name' => 'admin',
                'nickname' => 'admin',
                'email' => 'admin@mailto.com',
                'email_verified_at' => now(), 
                'password' => Hash::make('admin'),
                'remember_token' => Str::random(10),
            ]
        );
        if (!$user1->hasRole('admin')) {
            $user1->assignRole($role1);
        }

        $user2 = User::firstOrCreate([
            'name' => 'Sandra',
            'email' => 'sandra@mailto.com',
            'email_verified_at' => now(),
            'password' => Hash::make('1234'),
            'remember_token' => Str::random(10),
        ]);
        if (!$user2->hasRole('player')) {
            $user2->assignRole($role2);
        }
        */
    }
}