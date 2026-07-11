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

        // 2. Seed 7 Official Roles
        $rolesData = [
            ['name' => 'Treasury',          'description' => 'Super Admin — pengelola keuangan, payroll, dan sistem akun pengguna.'],
            ['name' => 'Headman',           'description' => 'Ketua organisasi — dapat melihat semua data keuangan dan menyetujui omset.'],
            ['name' => 'Marketing',         'description' => 'Tim pemasaran — dapat mengajukan omset dan mengelola event pemasaran.'],
            ['name' => 'Customer Service',  'description' => 'Tim layanan pelanggan — mengelola interaksi dan kebutuhan klien.'],
            ['name' => 'Penasehat',         'description' => 'Penasihat organisasi — akses baca penuh untuk membantu pengambilan keputusan.'],
            ['name' => 'System Developer',  'description' => 'Developer sistem — mengelola task teknis, dokumen, dan tools pengembangan.'],
            ['name' => 'Front-man Officer', 'description' => 'Petugas operasional — koordinasi dan manajemen operasional kantor harian.'],
        ];

        $roleModels = [];
        foreach ($rolesData as $roleData) {
            $roleModels[$roleData['name']] = Role::updateOrCreate(
                ['name' => $roleData['name']],
                $roleData
            );
        }

        // 3. Seed 7 Users mapped to new roles
        $usersData = [
            [
                'name'     => 'Thoriq Muhammad Pasya',
                'username' => 'treasury',
                'email'    => 'thoriq@company.com',
                'role_id'  => $roleModels['Treasury']->id,
                'password' => Hash::make('password'),
            ],
            [
                'name'     => 'Achmad Syahmi Rasendrya',
                'username' => 'achmad',
                'email'    => 'achmad@company.com',
                'role_id'  => $roleModels['Headman']->id,
                'password' => Hash::make('password'),
            ],
            [
                'name'     => 'RIzky Fadlurrohman',
                'username' => 'rizky',
                'email'    => 'rizky@company.com',
                'role_id'  => $roleModels['Marketing']->id,
                'password' => Hash::make('password'),
            ],
            [
                'name'     => 'Nafis Faturrahman',
                'username' => 'nafis',
                'email'    => 'nafis@company.com',
                'role_id'  => $roleModels['Customer Service']->id,
                'password' => Hash::make('password'),
            ],
            [
                'name'     => 'Surya Agung',
                'username' => 'surya',
                'email'    => 'surya@company.com',
                'role_id'  => $roleModels['Penasehat']->id,
                'password' => Hash::make('password'),
            ],
            [
                'name'     => 'Anggia Chrisanti',
                'username' => 'anggia',
                'email'    => 'anggia@company.com',
                'role_id'  => $roleModels['System Developer']->id,
                'password' => Hash::make('password'),
            ],
            [
                'name'     => 'Sayyidah Athazya El-Aldhiya',
                'username' => 'sayyidah',
                'email'    => 'sayyidah@company.com',
                'role_id'  => $roleModels['Front-man Officer']->id,
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
