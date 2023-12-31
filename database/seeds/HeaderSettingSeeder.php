<?php

use Illuminate\Database\Seeder;

class HeaderSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\HeaderSettings::create(
            [
                'app_name' => 'SIMKLUR',
                'title' => 'Sistem Informasi Masyarakat Kelurahan',
                'subtitle' => 'Pendataan Penduduk di SIMKLUR',
            ]
        );
    }
}
