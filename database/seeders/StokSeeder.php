<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StokSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data =
        [
            [
                'stok_id' => 1,
                'supplier_id' => 1,
                'barang_id' => 1,
                'user_id' => 2,
                'stok_tanggal' => '2024-02-01 08:15:30',
                'stok_jumlah' => 15,
            ],
            [
                'stok_id' => 2,
                'supplier_id' => 1,
                'barang_id' => 2,
                'user_id' => 2,
                'stok_tanggal' => '2024-02-03 14:20:10',
                'stok_jumlah' => 18,
            ],
            [
                'stok_id' => 3,
                'supplier_id' => 1,
                'barang_id' => 3,
                'user_id' => 2,
                'stok_tanggal' => '2024-02-05 09:45:55',
                'stok_jumlah' => 20,
            ],
            [
                'stok_id' => 4,
                'supplier_id' => 1,
                'barang_id' => 4,
                'user_id' => 2,
                'stok_tanggal' => '2024-02-07 17:30:20',
                'stok_jumlah' => 13,
            ],
            [
                'stok_id' => 5,
                'supplier_id' => 1,
                'barang_id' => 5,
                'user_id' => 2,
                'stok_tanggal' => '2024-02-09 06:10:05',
                'stok_jumlah' => 16,
            ],
            [
                'stok_id' => 6,
                'supplier_id' => 2,
                'barang_id' => 6,
                'user_id' => 3,
                'stok_tanggal' => '2024-02-11 12:00:45',
                'stok_jumlah' => 20,
            ],
            [
                'stok_id' => 7,
                'supplier_id' => 2,
                'barang_id' => 7,
                'user_id' => 3,
                'stok_tanggal' => '2024-02-13 20:45:10',
                'stok_jumlah' => 25,
            ],
            [
                'stok_id' => 8,
                'supplier_id' => 2,
                'barang_id' => 8,
                'user_id' => 3,
                'stok_tanggal' => '2024-02-15 05:30:55',
                'stok_jumlah' => 22,
            ],
            [
                'stok_id' => 9,
                'supplier_id' => 2,
                'barang_id' => 9,
                'user_id' => 3,
                'stok_tanggal' => '2024-02-17 11:15:20',
                'stok_jumlah' => 18,
            ],
            [
                'stok_id' => 10,
                'supplier_id' => 2,
                'barang_id' => 10,
                'user_id' => 3,
                'stok_tanggal' => '2024-02-19 15:50:35',
                'stok_jumlah' => 14,
            ],
            [
                'stok_id' => 11,
                'supplier_id' => 3,
                'barang_id' => 11,
                'user_id' => 1,
                'stok_tanggal' => '2024-02-21 09:25:10',
                'stok_jumlah' => 17,
            ],
            [
                'stok_id' => 12,
                'supplier_id' => 3,
                'barang_id' => 12,
                'user_id' => 1,
                'stok_tanggal' => '2024-02-23 18:10:50',
                'stok_jumlah' => 19,
            ],
            [
                'stok_id' => 13,
                'supplier_id' => 3,
                'barang_id' => 13,
                'user_id' => 1,
                'stok_tanggal' => '2024-02-25 07:35:45',
                'stok_jumlah' => 25,
            ],
            [
                'stok_id' => 14,
                'supplier_id' => 3,
                'barang_id' => 14,
                'user_id' => 1,
                'stok_tanggal' => '2024-02-27 14:55:30',
                'stok_jumlah' => 25,
            ],
            [
                'stok_id' => 15,
                'supplier_id' => 3,
                'barang_id' => 15,
                'user_id' => 1,
                'stok_tanggal' => '2024-02-29 21:40:15',
                'stok_jumlah' => 23,
            ],
        ];
        DB::table('t_stok')->insert($data);
    }
}
