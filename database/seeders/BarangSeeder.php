<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data =
        [
            [
                'barang_id' => 1,
                'kategori_id' => 4,
                'barang_kode' => 1,
                'barang_nama' => 'Pudding Fla Keju',
                'harga_beli' => 4000,
                'harga_jual' => 8000,
            ],
            [
                'barang_id' => 2,
                'kategori_id' => 4,
                'barang_kode' => 2,
                'barang_nama' => 'Pudding Mangga',
                'harga_beli' => 4500,
                'harga_jual' => 10000,
            ],
            [
                'barang_id' => 3,
                'kategori_id' => 4,
                'barang_kode' => 3,
                'barang_nama' => 'Es Krim Duren',
                'harga_beli' => 6000,
                'harga_jual' => 12000,
            ],
            [
                'barang_id' => 4,
                'kategori_id' => 5,
                'barang_kode' => 4,
                'barang_nama' => 'Cheesecake',
                'harga_beli' => 75000,
                'harga_jual' => 15000,
            ],
            [
                'barang_id' => 5,
                'kategori_id' => 5,
                'barang_kode' => 5,
                'barang_nama' => 'Bolu Pisang',
                'harga_beli' => 2000,
                'harga_jual' => 5000,
            ],
            [
                'barang_id' => 6,
                'kategori_id' => 1,
                'barang_kode' => 6,
                'barang_nama' => 'Brokoli Impor',
                'harga_beli' => 6000,
                'harga_jual' => 10000,
            ],
            [
                'barang_id' => 7,
                'kategori_id' => 1,
                'barang_kode' => 7,
                'barang_nama' => 'Selada Air',
                'harga_beli' => 1000,
                'harga_jual' => 2000,
            ],
            [
                'barang_id' => 8,
                'kategori_id' => 1,
                'barang_kode' => 8,
                'barang_nama' => 'Sawi Putih',
                'harga_beli' => 1500,
                'harga_jual' => 3000,
            ],
            [
                'barang_id' => 9,
                'kategori_id' => 2,
                'barang_kode' => 9,
                'barang_nama' => 'Apel Malang',
                'harga_beli' => 6000,
                'harga_jual' => 12000,
            ],
            [
                'barang_id' => 10,
                'kategori_id' => 2,
                'barang_kode' => 10,
                'barang_nama' => 'Jeruk Taiwan',
                'harga_beli' => 10000,
                'harga_jual' => 20000,
            ],
            [
                'barang_id' => 11,
                'kategori_id' => 2,
                'barang_kode' => 11,
                'barang_nama' => 'Rambutan Binjai',
                'harga_beli' => 3000,
                'harga_jual' => 6000,
            ],
            [
                'barang_id' => 12,
                'kategori_id' => 3,
                'barang_kode' => 12,
                'barang_nama' => 'Daging Ayam Dada Filet',
                'harga_beli' => 30000,
                'harga_jual' => 50000,
            ],
            [
                'barang_id' => 13,
                'kategori_id' => 3,
                'barang_kode' => 13,
                'barang_nama' => 'Ayam Utuh',
                'harga_beli' => 35000,
                'harga_jual' => 60000,
            ],
            [
                'barang_id' => 14,
                'kategori_id' => 3,
                'barang_kode' => 14,
                'barang_nama' => 'Daging Sapi Lokal',
                'harga_beli' => 100000,
                'harga_jual' => 125000,
            ],
            [
                'barang_id' => 15,
                'kategori_id' => 3,
                'barang_kode' => 15,
                'barang_nama' => 'Daging Sapi Impor',
                'harga_beli' => 135000,
                'harga_jual' => 200000,
            ],
        ];
        DB::table('m_barang')->insert($data);
    }
}
