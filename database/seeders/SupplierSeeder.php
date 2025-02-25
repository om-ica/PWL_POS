<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data =
        [
            [
                'supplier_id' => 1,
                'supplier_kode' => 1,
                'supplier_nama' => 'Dewa Dewi',
                'supplier_alamat' => 'Pasar Besar Malang',
            ],
            [
                'supplier_id' => 2,
                'supplier_kode' => 2,
                'supplier_nama' => 'Tumpang Selalu Pagi',
                'supplier_alamat' => 'Pasar Tumpang',
            ],
            [
                'supplier_id' => 3,
                'supplier_kode' => 3,
                'supplier_nama' => 'Tasup Kulakan',
                'supplier_alamat' => 'Pasar Induk Gadang',
            ],
        ];
        DB::table('m_supplier')->insert($data);
    }
}
