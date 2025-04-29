<?php

namespace App\Http\Controllers;

use App\Models\BarangModel;
use App\Models\StokModel;
use App\Models\SupplierModel;
use App\Models\UserModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yajra\DataTables\Facades\DataTables;

class StokController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Stok Barang',
            'list' => ['Home', 'Stok Barang']
        ];
    
        $page = (object) [
            'title' => 'Daftar stok barang yang terdaftar dalam sistem'
        ];
    
        $activeMenu = 'stok';
    
        $supplier = SupplierModel::select('supplier_id', 'supplier_nama')->get();
        $barang = BarangModel::select('barang_id', 'barang_nama')->get();
        $user = UserModel::select('user_id', 'username')->get();
    
        return view('stok.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
            'supplier' => $supplier,
            'barang' => $barang,
            'user' => $user
        ]);
    }

    // Ambil data stok barang dalam bentuk json untuk datatables 
    public function list(Request $request) 
    { 
        $stok = StokModel::select('stok_id', 'supplier_id', 'barang_id', 'stok_jumlah', 'stok_tanggal', 'user_id') 
                    ->with('supplier')
                    ->with('barang')
                    ->with('user');

        // Terapkan filter jika ada
        if ($request->supplier_id) {
            $stok->where('supplier_id', $request->supplier_id);
        }
        if ($request->barang_id) {
            $stok->where('barang_id', $request->barang_id);
        }
        if ($request->user_id) {
            $stok->where('user_id', $request->user_id);
        }

        return DataTables::of($stok) 
            ->addIndexColumn()  
            ->addColumn('aksi', function ($stok) {  
                $btn  = '<button onclick="modalAction(\''.url('/stok/' . $stok->stok_id . '/show_ajax').'\')" class="btn btn-info btn-sm">Detail</button>'; 
                $btn .= '<button onclick="modalAction(\''.url('/stok/' . $stok->stok_id . '/edit_ajax').'\')" class="btn btn-warning btn-sm">Edit</button>'; 
                $btn .= '<button onclick="modalAction(\''.url('/stok/' . $stok->stok_id . '/delete_ajax').'\')" class="btn btn-danger btn-sm">Hapus</button>';      
                return $btn; 
            }) 
            ->rawColumns(['aksi']) 
            ->make(true); 
    }

    // Mengembalikan view create ajax untuk stok
    public function create_ajax() 
    {
        $supplier = SupplierModel::select('supplier_id', 'supplier_nama')->get();
        $barang = BarangModel::select('barang_id', 'barang_nama')->get();

        return view('stok.create_ajax')
                    ->with('supplier', $supplier)
                    ->with('barang', $barang);
    }

    // Memproses tambah data stok
    public function store_ajax(Request $request) 
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'supplier_id'    => 'required|exists:m_supplier,supplier_id',
                'barang_id'      => 'required|exists:m_barang,barang_id',
                'stok_jumlah'    => 'required|integer|min:0'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status'   => false,
                    'message'  => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }

            // Tambahkan user_id dan stok_tanggal secara otomatis
            $data = $request->all();
            $data['user_id'] = auth()->user()->user_id; // Ambil ID user yang sedang login

            StokModel::create($data);
            return response()->json([
                'status'  => true,
                'message' => 'Data stok berhasil disimpan'
            ]);
        }

        return redirect('/');
    }

    // menampilkan detail stok barang menggunakan ajax
    public function show_ajax(string $id)
    {
        $stok = StokModel::with('supplier', 'barang', 'user')->find($id);

        if (!$stok) {
            return response()->json(['error' => 'Data stok tidak ditemukan'], 404);
        }

        return view('stok.show_ajax', ['stok' => $stok]);
    }

    // mengembalikan view edit ajax
    public function edit_ajax(string $id)
    {
        $stok = StokModel::find($id);
        $barang = BarangModel::select('barang_id', 'barang_nama')->get();
        $user = UserModel::select('user_id', 'nama')->get();
        $supplier = SupplierModel::select('supplier_id', 'supplier_nama')->get(); // Ambil daftar supplier

        return view('stok.edit_ajax', [
            'stok' => $stok,
            'barang' => $barang,
            'user' => $user,
            'supplier' => $supplier,
        ]);
    }

    // menyimpan edit
    public function update_ajax(Request $request, string $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'supplier_id'    => 'required|integer|exists:m_supplier,supplier_id',
                'barang_id'      => 'required|integer|exists:m_barang,barang_id',
                'stok_tanggal'   => 'required|date',
                'stok_jumlah'    => 'required|integer|min:1'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal.',
                    'msgField' => $validator->errors()
                ]);
            }

            $check = StokModel::find($id);
            if ($check) {
                // Tambahkan user_id secara otomatis
                $data = $request->all();
                $data['user_id'] = auth()->user()->user_id; // Ambil ID user yang sedang login

                $check->update($data);
                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil diupdate'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }
        }
        return redirect('/');
    }

    // mengembalikan view delete
    public function confirm_ajax(string $id)
    {
        $stok = StokModel::find($id);

        return view('stok.confirm_ajax', ['stok' => $stok]);
    }

    // mengelola delete
    public function delete_ajax(Request $request, $id)
    {
        // cek apakah request dari ajax
        if ($request->ajax() || $request->wantsJson()) {
            $stok = StokModel::find($id);
            if ($stok) {
                try {
                    $stok->delete();
                    return response()->json([
                        'status' => true,
                        'message' => 'Data berhasil dihapus'
                    ]);
                } catch (\Illuminate\Database\QueryException $e) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Data tidak bisa dihapus'
                    ]);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }
        }
        return redirect('/');
    }

    public function import() 
    { 
        return view('stok.import'); 
    }

    public function import_ajax(Request $request) 
    { 
        if ($request->ajax() || $request->wantsJson()) {
            // Validasi file
            $rules = [
                'file_stok' => ['required', 'mimes:xlsx', 'max:1024']
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            try {
                $file = $request->file('file_stok'); // Ambil file dari request

                // Load file Excel menggunakan PhpSpreadsheet
                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
                $reader->setReadDataOnly(true);
                $spreadsheet = $reader->load($file->getRealPath());
                $sheet = $spreadsheet->getActiveSheet();
                $data = $sheet->toArray(null, false, true, true);

                $insert = [];
                $errors = [];
                if (count($data) > 1) { // Pastikan ada data selain header
                    foreach ($data as $baris => $value) {
                        if ($baris <= 1) continue; // Lewati baris header

                        // Validasi data per baris
                        $rowData = [
                            'supplier_id'  => $value['A'], // Kolom A: Supplier (supplier_id)
                            'barang_id'    => $value['B'], // Kolom B: Nama Barang (barang_id)
                            'stok_jumlah'  => $value['C'], // Kolom C: Jumlah Stok
                        ];

                        $rules = [
                            'supplier_id'    => 'required|exists:m_supplier,supplier_id',
                            'barang_id'      => 'required|exists:m_barang,barang_id',
                            'stok_jumlah'    => 'required|integer|min:0'
                        ];

                        $validator = Validator::make($rowData, $rules);
                        if ($validator->fails()) {
                            $errors[$baris] = $validator->errors()->all();
                            continue; // Lewati baris ini jika validasi gagal
                        }

                        $insert[] = [
                            'supplier_id'  => $value['A'],
                            'barang_id'    => $value['B'],
                            'stok_jumlah'  => $value['C'],
                            'user_id'      => auth()->user()->user_id, // Otomatis user yang login
                            'stok_tanggal' => now(), // Otomatis tanggal saat ini
                            'created_at'   => now(),
                            'updated_at'   => now(),
                        ];
                    }

                    if (count($errors) > 0) {
                        return response()->json([
                            'status'  => false,
                            'message' => 'Ada data yang tidak valid',
                            'errors'  => $errors
                        ]);
                    }

                    if (count($insert) > 0) {
                        // Insert data ke database
                        StokModel::insert($insert);
                        return response()->json([
                            'status'  => true,
                            'message' => 'Data stok berhasil diimport'
                        ]);
                    } else {
                        return response()->json([
                            'status'  => false,
                            'message' => 'Tidak ada data yang valid untuk diimport'
                        ]);
                    }
                } else {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Tidak ada data yang diimport'
                    ]);
                }
            } catch (\Exception $e) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
                ], 500);
            }
        }
        return redirect('/');
    }

    public function export_excel()
    {
        $stok = StokModel::select(
            'barang_id',
            'user_id',
            'supplier_id',
            'stok_tanggal',
            'stok_jumlah'
        )
        ->orderBy('stok_id')
        ->with(['barang', 'user', 'supplier'])
        ->get();

        //load library excel
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet(); 

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Tanggal Stok');
        $sheet->setCellValue('C1', 'Nama Supplier');
        $sheet->setCellValue('D1', 'Nama Barang');
        $sheet->setCellValue('E1', 'Jumlah Stok');
        $sheet->setCellValue('F1', 'User');

        $sheet->getStyle('A1:F1')->getFont()->setBold(true); 

        $no = 1; 
        $baris = 2; 
        foreach ($stok as $key => $value) {
            $sheet->setCellValue('A' . $baris, $no);
            $sheet->setCellValue('B' . $baris, $value->stok_tanggal);
            $sheet->setCellValue('C' . $baris, $value->supplier->supplier_nama);
            $sheet->setCellValue('D' . $baris, $value->barang->barang_nama);
            $sheet->setCellValue('E' . $baris, $value->stok_jumlah);
            $sheet->setCellValue('F' . $baris, $value->user->nama);
            $no++;
            $baris++;
        }

        foreach (range('A', 'F') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true); 
        }

        $sheet->setTitle('Data Stock Barang'); 
        $writer = IOFactory ::createWriter($spreadsheet, 'Xlsx'); 
        $filename = 'Data_Stock_Barang_' . date('Y-m-d_H-i-s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        $writer->save('php://output'); 
        exit; 
    }

    public function export_pdf(){
        $stok = StokModel::select(
            'barang_id',
            'user_id',
            'supplier_id',
            'stok_tanggal',
            'stok_jumlah'
        )
        ->orderBy('barang_id')
        ->orderBy('stok_tanggal')
        ->with(['barang', 'user', 'supplier'])
        ->get();

        $pdf = Pdf::loadView('stok.export_pdf', ['stok' => $stok]);
        $pdf->setPaper('A4', 'portrait'); 
        $pdf->setOption("isRemoteEnabled", true); 
        $pdf->render();
        return $pdf->stream('Data Stock Barang '.date('Y-m-d H-i-s').'.pdf');
    }
}
