<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BarangModel extends Model
{
    use HasFactory; // Tambahkan trait HasFactory

    protected $table = 'm_barang';
    protected $primaryKey = 'barang_id';
    protected $fillable = ['kategori_id', 'barang_kode', 'barang_nama', 'harga_beli', 'harga_jual'];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriModel::class, 'kategori_id', 'kategori_id');
    }

    // Tambahkan method realStok()
    public function realStok()
    {
        $totalStok = StokModel::where('barang_id', $this->barang_id)
            ->sum('stok_jumlah') ?? 0;

        $totalTerjual = PenjualanDetailModel::where('barang_id', $this->barang_id)
            ->sum('jumlah') ?? 0;

        return (int) $totalStok - (int) $totalTerjual;
    }
}