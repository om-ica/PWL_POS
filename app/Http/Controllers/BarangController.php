<?php

namespace App\Http\Controllers;

use App\Models\BarangModel;
use App\Models\KategoriModel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yajra\DataTables\Facades\DataTables;

class BarangController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar barang',
            'list' => ['Home', 'barang']
        ];

        $page = (object) [
            'title' => 'Daftar barang yang terdaftar dalam sistem'
        ];

        $activeMenu = 'barang'; // set menu yang sedang aktif

        $kategori = KategoriModel::all(); // ambil data kategori untuk Filter kategori

        return view('barang.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'kategori' => $kategori, 'activeMenu' => $activeMenu]);
    }

    // Ambil data barang dalam bentuk json untuk datatables 
    public function list(Request $request) 
    { 
        $barang = BarangModel::select('barang_id', 'barang_kode', 'barang_nama', 'harga_beli', 'harga_jual', 'kategori_id') 
                    ->with('kategori');
                    
        // Filter data barang berdasarkan kategori_id
        if ($request->kategori_id) {
            $barang->where('kategori_id', $request->kategori_id);
        }
    
        return DataTables::of($barang) 
            // menambahkan kolom index / no urut (default nama kolom: DT_RowIndex) 
            ->addIndexColumn()  
            ->addColumn('aksi', function ($barang) {  // menambahkan kolom aksi 
                $btn  = '<button onclick="modalAction(\''.url('/barang/' . $barang->barang_id . '/show_ajax').'\')" class="btn btn-info btn-sm">Detail</button>'; 
                $btn .= '<button onclick="modalAction(\''.url('/barang/' . $barang->barang_id . '/edit_ajax').'\')" class="btn btn-warning btn-sm">Edit</button>'; 
                $btn .= '<button onclick="modalAction(\''.url('/barang/' . $barang->barang_id . '/delete_ajax').'\')"  class="btn btn-danger btn-sm">Hapus</button>';      
                return $btn; 
            }) 
            ->rawColumns(['aksi']) // memberitahu bahwa kolom aksi adalah html 
            ->make(true); 
    }
    
    public function create()
    {
        $breadcrumb = (object) [
            'title' => 'Tambah barang',
            'list' => ['Home', 'barang', 'Tambah']
        ];

        $page = (object) [
            'title' => 'Tambah barang Baru'
        ];

        $kategori = KategoriModel::all(); // ambil data kategori untuk ditampilkan di form
        $activeMenu = 'barang'; // set menu yang sedang aktif

        return view('barang.create', ['breadcrumb' => $breadcrumb, 'page' => $page, 'kategori' => $kategori, 'activeMenu' => $activeMenu]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'barang_kode'     => 'required|string|max:10|unique:m_barang,barang_id', // kode maksimal memiliki 10 karakter dan harus unik 
            'barang_nama'     => 'required|string|max:100', // nama harus diisi, berupa string, dan maksimal 100 karakter
            'harga_beli'      => 'required|integer',        // harga beli harus diisi menggunakan integer
            'harga_jual'      => 'required|integer',        // harga jual harus diisi menggunakan integer
            'kategori_id'     => 'required|integer'         // kategori_id harus dan berupa angka
        ]);

        BarangModel::create([
            'barang_kode'     => $request->barang_kode,
            'barang_nama'     => $request->barang_nama,
            'harga_beli'      => $request->harga_beli,
            'harga_jual'      => $request->harga_jual,
            'kategori_id'     => $request->kategori_id
        ]);

        return redirect('/barang')->with('succes', 'Data barang berhasil disimpan');
    }

    // mengembalikan view 
    public function create_ajax() 
    {
        $kategori = KategoriModel::select('kategori_id', 'kategori_nama')->get();

        return view('barang.create_ajax')
                    ->with('kategori', $kategori);
    }

    public function store_ajax(Request $request) {
        // cek apakah request berupa ajax
        if($request->ajax() || $request->wantsJson()){
            $rules = [
                'barang_kode'     => 'required|string|max:10|unique:m_barang,barang_id', // kode maksimal memiliki 10 karakter dan harus unik 
                'barang_nama'     => 'required|string|max:100', // nama harus diisi, berupa string, dan maksimal 100 karakter
                'harga_beli'      => 'required|integer',        // harga beli harus diisi menggunakan integer
                'harga_jual'      => 'required|integer',        // harga jual harus diisi menggunakan integer
                'kategori_id'     => 'required|integer'         // kategori_id harus dan berupa angka
            ];
    
            // use Illuminate\Support\Facades\Validator;
            $validator = Validator::make($request->all(), $rules);
    
            if($validator->fails()){
                return response()->json([
                    'status' => false, // response status, false: error/gagal, true: berhasil
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors(), // pesan error validasi
                ]);
            }
    
            BarangModel::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Data barang berhasil disimpan'
            ]);
        }
    
        redirect('/');
    }

    public function show(string $id)
    {
        $barang =BarangModel::with('kategori')->find($id);

        $breadcrumb = (object) [
            'title' => 'Detail barang',
            'list'  => ['Home', 'barang', 'Detail']
        ];

        $page = (object) [
            'title' => 'Detail barang'
        ];

        $activeMenu = 'barang'; // set menu yang sedang aktif

        return view('barang.show', ['breadcrumb' => $breadcrumb, 'page' => $page, 'barang' => $barang, 'activeMenu' => $activeMenu]);
    }

    // menampilkan detail barang menggunakan ajax
    public function show_ajax(string $id)
    {
        $barang = BarangModel::with('kategori')->find($id);

        return view('barang.show_ajax', ['barang' => $barang]);
    }

    // Menampilkan halaman form edit barang
    public function edit(string $id)
    {
        $barang = BarangModel::find($id);
        $kategori = KategoriModel::all();

        $breadcrumb = (object) [
            'title' => 'Edit barang',
            'list' => ['Home', 'barang', 'Edit']
        ];

        $page = (object) [
            'title' => 'Edit barang'
        ];

        $activeMenu = 'barang'; // set menu yang sedang aktif

        return view('barang.edit', ['breadcrumb' => $breadcrumb, 'page' => $page, 'barang' => $barang, 'kategori' => $kategori, 'activeMenu' => $activeMenu]);
    }

    // Menyimpan perubahan data barang
    public function update(Request $request, string $id)
    {
        $request->validate([
            'barang_kode'     => 'required|string|max:10|unique:m_barang,barang_id,'.$id.',barang_id', // kode maksimal memiliki 10 karakter dan harus unik 
            'barang_nama'     => 'required|string|max:100', // nama harus diisi, berupa string, dan maksimal 100 karakter
            'harga_beli'      => 'required|integer',        // harga beli harus diisi menggunakan integer
            'harga_jual'      => 'required|integer',        // harga jual harus diisi menggunakan integer
            'kategori_id'     => 'required|integer'         // kategori_id harus dan berupa angka
        ]);

        BarangModel::find($id)->update([
            'barang_kode'     => $request->barang_kode,
            'barang_nama'     => $request->barang_nama,
            'harga_beli'      => $request->harga_beli,
            'harga_jual'      => $request->harga_jual,
            'kategori_id'     => $request->kategori_id
        ]);

        return redirect('/barang')->with('success', 'Data barang berhasil diubah');
    }

    // mengembalikan view ke edit_ajax
    public function edit_ajax(string $id) 
    {
        $barang = BarangModel::find($id);
        $kategori = KategoriModel::select('kategori_id', 'kategori_nama')->get();

        return view('barang.edit_ajax', ['barang' => $barang, 'kategori' => $kategori]);
    }

    // fungsi untuk menyimpan data user yang telah diubah menggunakan ajax
    public function update_ajax(Request $request, $id){ 
        if ($request->ajax() || $request->wantsJson()) { 
            $rules = [ 
                'kategori_id' => 'required|integer', 
                'barang_kode' => 'required|max:20|unique:m_barang,barang_kode,'.$id.',barang_id', 
                'barang_nama' => 'required|max:100', 
                'harga_beli'  => 'required|integer|min:0',
                'harga_jual'  => 'required|integer|min:0', 
            ]; 
         
            $validator = Validator::make($request->all(), $rules); 
    
            if ($validator->fails()) { 
                return response()->json([ 
                    'status'   => false,
                    'message'  => 'Validasi gagal.', 
                    'msgField' => $validator->errors()
                ]); 
            } 
    
            $check = BarangModel::find($id); 
            if ($check) { 
                if(!$request->filled('password') ){
                    $request->request->remove('password'); 
                } 
                
                $check->update($request->all()); 
                return response()->json([ 
                    'status'  => true, 
                    'message' => 'Data berhasil diupdate' 
                ]); 
            } else{ 
                return response()->json([ 
                    'status'  => false, 
                    'message' => 'Data tidak ditemukan' 
                ]); 
            } 
        } 
        return redirect('/'); 
    } 

    // Menghapus data barang
    public function destroy(string $id)
    {
        $check = BarangModel::find($id);
        if (!$check) { // untuk mengecek apakah data barang dengan id yang dimaksud ada atau tidak
            return redirect('/barang')->with('error', 'Data barang tidak ditemukan');
        }

        try {
            BarangModel::destroy($id); // unttuk menghapus data kategori

            return redirect('/barang')->with('success', 'Data barang berhasil dihapus');
        } catch (\Illuminate\Database\QueryException $e) {
            // Jika terjadi error ketika menghapus data, redirect kembali ke halaman dengan membawa pesan error
            return redirect('/barang')->with('error', 'Data barang gagal karena masih terdapat tabel lain yang terkait dengan data ini');
        }
    }

    // mengembalikan view confirm_ajax
    public function confirm_ajax(string $id)
    {
        $barang = BarangModel::find($id);

        return view('barang.confirm_ajax', ['barang' => $barang]);
    }

    public function delete_ajax(Request $request, $id)
    {
        // cek apakah request dari ajax
        if ($request->ajax() || $request->wantsJson()) {
            $barang = BarangModel::find($id);
            if ($barang) {
                $barang->delete();
                return response()->json([
                    'status'  => true,
                    'massage' => 'Data berhasil dihapus' 
                ]);
            } else {
                return response()->json([
                    'status'  => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }
        }
        return redirect('/');
    }

    public function import() 
    { 
        return view('barang.import'); 
    } 

    public function import_ajax(Request $request) 
    { 
        if($request->ajax() || $request->wantsJson()){ 
            $rules = [ 
                // validasi file harus xls atau xlsx, max 1MB 
                'file_barang' => ['required', 'mimes:xlsx', 'max:1024'] 
            ]; 
 
            $validator = Validator::make($request->all(), $rules); 
            if($validator->fails()){ 
                return response()->json([ 
                    'status' => false, 
                    'message' => 'Validasi Gagal', 
                    'msgField' => $validator->errors() 
                ]); 
            } 
 
            $file = $request->file('file_barang');  // ambil file dari request 
 
            $reader = IOFactory::createReader('Xlsx');  // load reader file excel 
            $reader->setReadDataOnly(true);             // hanya membaca data 
            $spreadsheet = $reader->load($file->getRealPath()); // load file excel 
            $sheet = $spreadsheet->getActiveSheet();    // ambil sheet yang aktif 
 
            $data = $sheet->toArray(null, false, true, true);   // ambil data excel 
 
            $insert = []; 
            if(count($data) > 1){ // jika data lebih dari 1 baris 
                foreach ($data as $baris => $value) { 
                    if($baris > 1){ // baris ke 1 adalah header, maka lewati 
                        $insert[] = [ 
                            'kategori_id' => $value['A'], 
                            'barang_kode' => $value['B'], 
                            'barang_nama' => $value['C'], 
                            'harga_beli' => $value['D'], 
                            'harga_jual' => $value['E'], 
                            'created_at' => now(), 
                        ]; 
                    } 
                } 
 
                if(count($insert) > 0){ 
                    // insert data ke database, jika data sudah ada, maka diabaikan 
                    BarangModel::insertOrIgnore($insert);    
                } 
 
                return response()->json([ 
                    'status' => true, 
                    'message' => 'Data berhasil diimport' 
                ]); 
            }else{ 
                return response()->json([ 
                    'status' => false, 
                    'message' => 'Tidak ada data yang diimport' 
                ]); 
            } 
        } 
        return redirect('/'); 
    } 
}
