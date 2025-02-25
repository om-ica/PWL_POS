<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = 
        [
            [
                'kategori_id' => 1,
                'kategori_kode' => 1,
                'kategori_nama' => 'Sayur',
            ],
            [
                'kategori_id' => 2,
                'kategori_kode' => 2,
                'kategori_nama' => 'Buah',
            ],
            [
                'kategori_id' => 3,
                'kategori_kode' => 3,
                'kategori_nama' => 'Daging',
            ],
            [
                'kategori_id' => 4,
                'kategori_kode' => 4,
                'kategori_nama' => 'Jajan',
            ],
            [
                'kategori_id' => 5,
                'kategori_kode' => 5,
                'kategori_nama' => 'Kue',
            ],
        ];
        DB::table('m_kategori')->insert($data);
    }
}
