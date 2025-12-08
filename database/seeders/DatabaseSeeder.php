<?php

namespace database\Seeders;

use Idev\EasyAdmin\app\Models\Role;
// use App\Models\SampleData;
use Illuminate\Database\Seeder;
use Illuminate\Foundation\Auth\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->role();
        $this->user();
        // $this->sampleData();
    }

    public function role()
    {
        Role::updateOrCreate(
            [
                'name' => 'programmer'
            ],
            [
                'name' => 'programmer',
                'access' => '[{"route":"dashboard","access":["list"]},{"route":"role","access":["list","create","show","edit","delete","import-excel-default","export-excel-default","export-pdf-default"]},{"route":"user","access":["list","create","show","edit","delete","import-excel-default","export-excel-default","export-pdf-default"]}]',
            ]
        );

        Role::updateOrCreate(
            [
                'name' => 'admin'
            ],
            [
                'name' => 'admin',
                'access' => '[{"route":"dashboard","access":["list"]},{"route":"role","access":["list","create","show","edit","delete","import-excel-default","export-excel-default","export-pdf-default"]},{"route":"user","access":["list","create","show","edit","delete","import-excel-default","export-excel-default","export-pdf-default"]}]',
            ]
        );

        Role::updateOrCreate(
            [
                'name' => 'Direktur'
            ],
            [
                'name' => 'direktur',
                'access' => '[{"route":"dashboard","access":["list"]}]',
            ]
        );

        Role::updateOrCreate(
            [
                'name' => 'Manager'
            ],
            [
                'name' => 'manager',
                'access' => '[{"route":"dashboard","access":["list"]}]',
            ]
        );

        Role::updateOrCreate(
            [
                'name' => 'Staff'
            ],
            [
                'name' => 'staff',
                'access' => '[{"route":"dashboard","access":["list"]}]',
            ]
        );

        Role::updateOrCreate(
            [
                'name' => 'Participant'
            ],
            [
                'name' => 'participant',
                'access' => '[{"route":"dashboard","access":["list"]}]',
            ]
        );

        Role::updateOrCreate(
            [
                'name' => 'Mentor'
            ],
            [
                'name' => 'mentor',
                'access' => '[{"route":"dashboard","access":["list"]}]',
            ]
        );
    }




    public function user()
    {
        User::updateOrCreate(
            [
                'email' => 'admin@idev.com',
            ],
            [
                'name' => 'NAYANTAKA',
                'email' => 'admin@idev.com',
                'company' => 'PT. SAMPHARINDO PERDANA',
                'divisi' => 'UMUM & SDM',
                'unit_kerja' => 'HRGA',
                'status' => 'BULANAN KONTRAK',
                'jk' => 'Laki-laki',
                'telp' => '0895832720752',
                'nik' => '3.251.141',
                'password' => bcrypt('qwerty'),
                'role_id' => Role::where('name', 'programmer')->first()->id,
            ]
        );
    }
}
