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
                'name' => 'developer'
            ],
            [
                'name' => 'developer',
                'access' => '[{"route":"dashboard","access":["list"]},{"route":"role","access":["list","create","show","edit","delete","import-excel-default","export-excel-default","export-pdf-default"]},{"route":"user","access":["list","create","show","edit","delete","import-excel-default","export-excel-default","export-pdf-default"]}]',
            ]
        );

        Role::updateOrCreate(
            [
                'name' => 'supervisi'
            ],
            [
                'name' => 'supervisi',
                'access' => '[{"route":"dashboard","access":["list"]},{"route":"role","access":["list","create","show","edit","delete","import-excel-default","export-excel-default","export-pdf-default"]},{"route":"user","access":["list","create","show","edit","delete","import-excel-default","export-excel-default","export-pdf-default"]}]',
            ]
        );

        Role::updateOrCreate(
            [
                'name' => 'administrator'
            ],
            [
                'name' => 'administrator',
                'access' => '[{"route":"dashboard","access":["list"]}]',
            ]
        );

        Role::updateOrCreate(
            [
                'name' => 'trainer'
            ],
            [
                'name' => 'trainer',
                'access' => '[{"route":"dashboard","access":["list"]}]',
            ]
        );

        Role::updateOrCreate(
            [
                'name' => 'participant'
            ],
            [
                'name' => 'participant',
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
                'role_id' => Role::where('name', 'developer')->first()->id,
            ]
        );
    }
}
