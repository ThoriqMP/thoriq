<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\KpiGrade;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed KPI Grades
        $grades = [
            ['grade_name' => 'A', 'weight_percentage' => 14.00],
            ['grade_name' => 'B', 'weight_percentage' => 9.00],
            ['grade_name' => 'C', 'weight_percentage' => 4.50],
        ];

        foreach ($grades as $grade) {
            KpiGrade::updateOrCreate(['grade_name' => $grade['grade_name']], $grade);
        }

        // 2. Seed Default Roles
        $roles = ['Treasury', 'Head', 'Sales', 'Developer'];
        $roleModels = [];
        foreach ($roles as $rName) {
            $roleModels[$rName] = Role::updateOrCreate(
                ['name' => $rName],
                ['name' => $rName, 'description' => "Role $rName"]
            );
        }

        // 3. Seed 7 Users mapped to seeded Role IDs
        $usersData = [
            [
                'name' => 'Thoriq Muhammad Pasya',
                'username' => 'treasury',
                'email' => 'thoriq@company.com',
                'role_id' => $roleModels['Treasury']->id,
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Achmad Syahmi Rasendrya',
                'username' => 'achmad',
                'email' => 'achmad@company.com',
                'role_id' => $roleModels['Head']->id,
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'RIzky Fadlurrohman',
                'username' => 'rizky',
                'email' => 'rizky@company.com',
                'role_id' => $roleModels['Sales']->id,
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Nafis Faturrahman',
                'username' => 'nafis',
                'email' => 'nafis@company.com',
                'role_id' => $roleModels['Developer']->id,
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Surya Agung',
                'username' => 'surya',
                'email' => 'surya@company.com',
                'role_id' => $roleModels['Developer']->id,
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Anggia Chrisanti',
                'username' => 'anggia',
                'email' => 'anggia@company.com',
                'role_id' => $roleModels['Developer']->id,
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Sayyidah Athazya El-Aldhiya',
                'username' => 'sayyidah',
                'email' => 'sayyidah@company.com',
                'role_id' => $roleModels['Developer']->id,
                'password' => Hash::make('password'),
            ],
        ];

        foreach ($usersData as $userData) {
            User::updateOrCreate(
                ['username' => $userData['username']],
                $userData
            );
        }
    }
}
