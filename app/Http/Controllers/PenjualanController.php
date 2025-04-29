<?php

namespace App\Http\Controllers;

use App\Models\PenjualanModel;
use App\Models\PenjualanDetailModel;
use App\Models\BarangModel;
use App\Models\UserModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Vtiful\Kernel\Excel;
use Yajra\DataTables\Facades\DataTables;

class PenjualanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Penjualan',
            'list' => ['Home', 'Penjualan']
        ];

        $page = (object) [
            'title' => 'Daftar transaksi penjualan yang terdaftar dalam sistem'
        ];

        $activeMenu = 'penjualan';

        return view('penjualan.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu
        ]);
    }

    public function list(Request $request)
    {
        $penjualan = PenjualanModel::select('penjualan_id', 'user_id', 'pembeli', 'penjualan_kode', 'penjualan_tanggal')
            ->with('user', 'details.barang');

        return DataTables::of($penjualan)
            ->addIndexColumn()
            ->addColumn('total_harga', function ($penjualan) {
                // Hitung total harga berdasarkan harga_jual dari m_barang
                $total = $penjualan->details->sum(function ($detail) {
                    return $detail->barang->harga_jual * $detail->jumlah;
                });
                return 'Rp ' . number_format($total, 0, ',', '.');
            })
            ->addColumn('aksi', function ($penjualan) {
                $btn = '<button onclick="modalAction(\''.url('/penjualan/' . $penjualan->penjualan_id . '/show_ajax').'\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\''.url('/penjualan/' . $penjualan->penjualan_id . '/edit_ajax').'\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\''.url('/penjualan/' . $penjualan->penjualan_id . '/delete_ajax').'\')" class="btn btn-danger btn-sm">Hapus</button>';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create_ajax()
    {
        $barang = BarangModel::select('barang_id', 'barang_nama', 'harga_jual')
            ->get()
            ->map(function ($b) {
                try {
                    $b->real_stok = $b->realStok();
                } catch (\Exception $e) {
                    $b->real_stok = 0;
                }
                return $b;
            });

        // Siapkan data JSON untuk JavaScript
        $barangData = $barang->map(function ($b) {
            return [
                'id' => $b->barang_id,
                'nama' => $b->barang_nama,
                'harga_jual' => (int) $b->harga_jual,
                'real_stok' => (int) $b->real_stok
            ];
        })->toArray();

        return view('penjualan.create_ajax', [
            'barang' => $barang,
            'barangData' => $barangData // Kirim data yang sudah dipetakan
        ]);
    }

    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'pembeli' => 'required|string|max:50',
                'penjualan_tanggal' => 'required|date',
                'items' => 'required|array|min:1',
                'items.*.barang_id' => 'required|exists:m_barang,barang_id',
                'items.*.jumlah' => 'required|integer|min:1',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }

            DB::beginTransaction();
            try {
                $lastPenjualan = PenjualanModel::latest()->first();
                $lastNumber = $lastPenjualan ? (int) substr($lastPenjualan->penjualan_kode, 3) : 0;
                $newKode = 'PJ' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

                $penjualan = PenjualanModel::create([
                    'user_id' => auth()->user()->user_id,
                    'pembeli' => $request->pembeli,
                    'penjualan_kode' => $newKode,
                    'penjualan_tanggal' => $request->penjualan_tanggal,
                ]);

                $items = $request->items;
                foreach ($items as $item) {
                    $barang = BarangModel::find($item['barang_id']);
                    $realStok = $barang->realStok();

                    if ($realStok < $item['jumlah']) {
                        DB::rollBack();
                        return response()->json([
                            'status' => false,
                            'message' => "Stok barang {$barang->barang_nama} tidak mencukupi. Stok tersedia: {$realStok}",
                        ]);
                    }

                    PenjualanDetailModel::create([
                        'penjualan_id' => $penjualan->penjualan_id,
                        'barang_id' => $item['barang_id'],
                        'harga' => $barang->harga_jual,
                        'jumlah' => $item['jumlah'],
                    ]);
                }

                DB::commit();
                return response()->json([
                    'status' => true,
                    'message' => 'Data penjualan berhasil disimpan'
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }
        }

        return redirect('/');
    }

    public function show_ajax(string $id)
    {
        $penjualan = PenjualanModel::with('user', 'details.barang')->find($id);

        if (!$penjualan) {
            return response()->json(['error' => 'Data penjualan tidak ditemukan'], 404);
        }

        return view('penjualan.show_ajax', ['penjualan' => $penjualan]);
    }

    public function edit_ajax(string $id)
    {
        $penjualan = PenjualanModel::with('details')->find($id);
        $barang = BarangModel::select('barang_id', 'barang_nama', 'harga_jual')
            ->get()
            ->map(function ($b) {
                try {
                    $b->real_stok = $b->realStok();
                } catch (\Exception $e) {
                    $b->real_stok = 0;
                }
                return $b;
            });

        // Siapkan data JSON untuk JavaScript
        $barangData = $barang->map(function ($b) {
            return [
                'id' => $b->barang_id,
                'nama' => $b->barang_nama,
                'harga_jual' => (int) $b->harga_jual,
                'real_stok' => (int) $b->real_stok
            ];
        })->toArray();

        if (!$penjualan) {
            return response()->json(['error' => 'Data penjualan tidak ditemukan'], 404);
        }

        return view('penjualan.edit_ajax', [
            'penjualan' => $penjualan,
            'barang' => $barang,
            'barangData' => $barangData // Kirim data yang sudah dipetakan
        ]);
    }

    public function update_ajax(Request $request, string $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'pembeli' => 'required|string|max:50',
                'penjualan_tanggal' => 'required|date',
                'items' => 'required|array|min:1',
                'items.*.barang_id' => 'required|exists:m_barang,barang_id',
                'items.*.jumlah' => 'required|integer|min:1',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal.',
                    'msgField' => $validator->errors()
                ]);
            }

            $penjualan = PenjualanModel::find($id);
            if (!$penjualan) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data penjualan tidak ditemukan'
                ]);
            }

            DB::beginTransaction();
            try {
                $penjualan->update([
                    'user_id' => auth()->user()->user_id,
                    'pembeli' => $request->pembeli,
                    'penjualan_tanggal' => $request->penjualan_tanggal,
                ]);

                $penjualan->details()->delete();

                $items = $request->items;
                foreach ($items as $item) {
                    $barang = BarangModel::find($item['barang_id']);
                    $realStok = $barang->realStok();

                    if ($realStok < $item['jumlah']) {
                        DB::rollBack();
                        return response()->json([
                            'status' => false,
                            'message' => "Stok barang {$barang->barang_nama} tidak mencukupi. Stok tersedia: {$realStok}",
                        ]);
                    }

                    PenjualanDetailModel::create([
                        'penjualan_id' => $penjualan->penjualan_id,
                        'barang_id' => $item['barang_id'],
                        'harga' => $barang->harga_jual,
                        'jumlah' => $item['jumlah'],
                    ]);
                }

                DB::commit();
                return response()->json([
                    'status' => true,
                    'message' => 'Data penjualan berhasil diupdate'
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }
        }

        return redirect('/');
    }

    public function confirm_ajax(string $id)
    {
        $penjualan = PenjualanModel::find($id);

        if (!$penjualan) {
            return response()->json(['error' => 'Data penjualan tidak ditemukan'], 404);
        }

        return view('penjualan.confirm_ajax', ['penjualan' => $penjualan]);
    }

    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $penjualan = PenjualanModel::find($id);
            if ($penjualan) {
                try {
                    $penjualan->details()->delete();
                    $penjualan->delete();
                    return response()->json([
                        'status' => true,
                        'message' => 'Data penjualan berhasil dihapus'
                    ]);
                } catch (\Illuminate\Database\QueryException $e) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Data tidak bisa dihapus karena masih digunakan'
                    ]);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data penjualan tidak ditemukan'
                ]);
            }
        }
        return redirect('/');
    }

    public function export_excel()
    {
        // Ambil data penjualan beserta detail dan barang
        $penjualan = PenjualanModel::with(['details.barang'])->get();

        // Gunakan library Maatwebsite\Excel untuk export
        return Excel::download(new class($penjualan) implements \Maatwebsite\Excel\Concerns\FromView {
            private $penjualan;

            public function __construct($penjualan)
            {
                $this->penjualan = $penjualan;
            }

            public function view(): View
            {
                return view('penjualan.export_excel', [
                    'penjualan' => $this->penjualan
                ]);
            }
        }, 'laporan-penjualan-' . date('YmdHis') . '.xlsx');
    }

    public function export_pdf()
    {
        // Ambil data penjualan beserta detail dan barang
        $penjualan = PenjualanModel::select(
            'penjualan_id',
            'penjualan_kode',
            'pembeli',
            'penjualan_tanggal'
        )
        ->orderBy('penjualan_id')
        ->orderBy('penjualan_tanggal')
        ->with(['details.barang'])
        ->get();

        // Gunakan library DomPDF untuk export
        $pdf = PDF::loadView('penjualan.export_pdf', [
            'penjualan' => $penjualan
        ]);

        // Atur orientasi dan ukuran kertas
        $pdf->setPaper('A4', 'landscape');
        $pdf->setOption("isRemoteEnabled", true);

        // Stream file PDF
        return $pdf->stream('Laporan_Penjualan_' . date('Y-m-d_H-i-s') . '.pdf');
    }
}