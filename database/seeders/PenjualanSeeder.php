<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenjualanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $data =
       [
            [
                'penjualan_id' => 1,
                'user_id' => 3,
                'pembeli' => 'Budiono Siregar',
                'penjualan_kode' => 1,
                'penjualan_tanggal' => '2024-03-03 11:10:12', 
            ],
            [   'penjualan_id' => 2, 
                'user_id' => 3, 
                'pembeli' => 'Siti Nurhaliza', 
                'penjualan_kode' => 2, 
                'penjualan_tanggal' => '2024-03-03 12:15:30'
            ],
            [   'penjualan_id' => 3, 
                'user_id' => 3, 
                'pembeli' => 'Ahmad Fauzi', 
                'penjualan_kode' => 3, 
                'penjualan_tanggal' => '2024-03-03 13:20:45'
            ],
            [   'penjualan_id' => 4, 
                'user_id' => 3, 
                'pembeli' => 'Joko Santoso', 
                'penjualan_kode' => 4, 
                'penjualan_tanggal' => '2024-03-04 09:45:10'
            ],
            [   'penjualan_id' => 5, 
                'user_id' => 2, 
                'pembeli' => 'Dewi Kartika', 
                'penjualan_kode' => 5, 'penjualan_tanggal' => '2024-03-04 14:30:20'
            ],
            [   'penjualan_id' => 6, 
                'user_id' => 2, 
                'pembeli' => 'Hendra Wijaya', 
                'penjualan_kode' => 6, 
                'penjualan_tanggal' => '2024-03-04 16:50:55'
            ],        
            [   'penjualan_id' => 7, 
                'user_id' => 2, 
                'pembeli' => 'Lina Sari', 
                'penjualan_kode' => 7, 
                'penjualan_tanggal' => '2024-03-05 08:10:05'
            ],
            [   'penjualan_id' => 8, 
                'user_id' => 1, 
                'pembeli' => 'Surya Dharma', 
                'penjualan_kode' => 8, 
                'penjualan_tanggal' => '2024-03-05 10:25:40'
            ],
            [   'penjualan_id' => 9, 
                'user_id' => 1, 
                'pembeli' => 'Budi Setiawan', 
                'penjualan_kode' => 9, 
                'penjualan_tanggal' => '2024-03-05 15:40:30'
            ],        
            [   'penjualan_id' => 10, 
                'user_id' => 1, 
                'pembeli' => 'Rina Permata', 
                'penjualan_kode' => 10, 
                'penjualan_tanggal' => '2024-03-06 11:55:15'
            ],
       ];
       DB::table('t_penjualan')->insert($data); 
    }
}
