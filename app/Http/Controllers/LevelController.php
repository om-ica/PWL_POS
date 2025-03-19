<?php

namespace App\Http\Controllers;

use App\Models\LevelModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Monolog\Level;
use Yajra\DataTables\Facades\DataTables;

class LevelController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Level',
            'list' => ['Home', 'Level']
        ];

        $page = (object) [
            'title' => 'Daftar level yang terdaftar dalam sistem'
        ];

        $activeMenu = 'level'; // set menu yang sedang aktif

        $level = LevelModel::all(); // ambil data level untuk Filter level

        return view('level.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'level' => $level, 'activeMenu' => $activeMenu]);
    }

    // Ambil data level dalam bentuk json untuk datatables 
    public function list(Request $request) 
    { 
        $levels = LevelModel::select('level_id', 'level_kode', 'level_nama');
        return DataTables::of($levels) 
            // menambahkan kolom index / no urut (default nama kolom: DT_RowIndex) 
            ->addIndexColumn()  
            ->addColumn('aksi', function ($level) {  // menambahkan kolom aksi
                $btn  = '<button onclick="modalAction(\''.url('/level/' . $level->level_id . '/show_ajax').'\')" class="btn btn-info btn-sm">Detail</button>'; 
                $btn .= '<button onclick="modalAction(\''.url('/level/' . $level->level_id . '/edit_ajax').'\')" class="btn btn-warning btn-sm">Edit</button>'; 
                $btn .= '<button onclick="modalAction(\''.url('/level/' . $level->level_id . '/delete_ajax').'\')"  class="btn btn-danger btn-sm">Hapus</button>';      
                return $btn; 
            }) 
            ->rawColumns(['aksi']) // memberitahu bahwa kolom aksi adalah html 
            ->make(true); 
    }
    
    public function create()
    {
        $breadcrumb = (object) [
            'title' => 'Tambah Level',
            'list' => ['Home', 'Level', 'Tambah']
        ];

        $page = (object) [
            'title' => 'Tambah Level Baru'
        ];

        $level = LevelModel::all(); // ambil data level untuk ditampilkan di form
        $activeMenu = 'level'; // set menu yang sedang aktif

        return view('level.create', ['breadcrumb' => $breadcrumb, 'page' => $page, 'level' => $level, 'activeMenu' => $activeMenu]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'level_kode' => 'required|string|max:10|unique:m_level,level_kode', // level_kode diisi maksimal 10 karakter dan harus unik
            'level_nama'     => 'required|string|max:100' // level_nama diisi maksimal 100 karakter
        ]);

        LevelModel::create([
            'level_kode' => $request -> level_kode,
            'level_nama' => $request -> level_nama
        ]);

        return redirect('/level')->with('success', 'Data level berhasil disimpan');
    }

    public function create_ajax() 
    {
        return view('level.create_ajax');
    }

    public function show(string $id)
    {
        $level =LevelModel::find($id);

        $breadcrumb = (object) [
            'title' => 'Detail level',
            'list'  => ['Home', 'level', 'Detail']
        ];

        $page = (object) [
            'title' => 'Detail level'
        ];

        $activeMenu = 'level'; // set menu yang sedang aktif

        return view('level.show', ['breadcrumb' => $breadcrumb, 'page' => $page, 'level' => $level, 'activeMenu' => $activeMenu]);
    }

    public function store_ajax(Request $request) {
        // cek apakah request berupa ajax
        if($request->ajax() || $request->wantsJson()){
            $rules = [
                'level_kode' => 'required|string|max:10|unique:m_level,level_kode', // level_kode diisi maksimal 10 karakter dan harus unik
                'level_nama'     => 'required|string|max:100' // level_nama diisi maksimal 100 karakter
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
    
            LevelModel::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Data user berhasil disimpan'
            ]);
        }
    
        redirect('/');
    }

    // untuk menampilkan detail level menggunakan ajax
    public function show_ajax(string $id)
    {
        $level = LevelModel::find($id);

        return view('level.show_ajax', ['level' => $level]);
    }

    // Menampilkan halaman form edit user
    public function edit(string $id)
    {
        $level = LevelModel::find($id);

        $breadcrumb = (object) [
            'title' => 'Edit Level',
            'list' => ['Home', 'Level', 'Edit']
        ];

        $page = (object) [
            'title' => 'Edit Level'
        ];

        $activeMenu = 'level'; // set menu yang sedang aktif

        return view('level.edit', ['breadcrumb' => $breadcrumb, 'page' => $page, 'level' => $level, 'activeMenu' => $activeMenu]);
    }

    // Menyimpan perubahan data level
    public function update(Request $request, string $id)
    {
        // username harus diisi, berupa string, minimal 3 karakter
        // dan bernilai unik di tabel m_user kolom username, kecuali untuk user dengan id yang sedang diedit
        $request->validate([
            'level_kode' => 'required|string|max:10|unique:m_level,level_kode,' .$id. ',level_id', // level_kode diisi maksimal 10 karakter dan harus unik
            'level_nama'     => 'required|string|max:100' // level_nama diisi maksimal 100 karakter   
        ]);

        LevelModel::find($id)->update([
            'level_kode' => $request -> level_kode,
            'level_nama' => $request -> level_nama
        ]);

        return redirect('/level')->with('success', 'Data level berhasil diubah');
    }

    // edit data level menggunakan ajax
    public function edit_ajax(string $id) 
    {
        $level = LevelModel::find($id);

        return view('level.edit_ajax', ['level' => $level]);
    }

    // fungsi untuk menyimpan data user yang telah diubah menggunakan ajax
    public function update_ajax(Request $request, $id){ 
        // cek apakah request dari ajax 
        if ($request->ajax() || $request->wantsJson()) { 
            $rules = [ 
                'level_kode' => 'required|string|max:10|unique:m_level,level_kode,' .$id. ',level_id', // level_kode diisi maksimal 10 karakter dan harus unik
                'level_nama' => 'required|string|max:100' // level_nama diisi maksimal 100 karakter 
            ];
     
            // use Illuminate\Support\Facades\Validator; 
            $validator = Validator::make($request->all(), $rules); 
     
            if ($validator->fails()) { 
                return response()->json([ 
                    'status'   => false,    // respon json, true: berhasil, false: gagal 
                    'message'  => 'Validasi gagal.', 
                    'msgField' => $validator->errors()  // menunjukkan field mana yang error 
                ]); 
            } 
     
            $check = LevelModel::find($id); 
            if ($check) {
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

    // Menghapus level user
    public function destroy(string $id)
    {
        $check = LevelModel::find($id);
        if (!$check) { // untuk mengecek apakah data user dengan id yang dimaksud ada atau tidak
            return redirect('/level')->with('error', 'Data level tidak ditemukan');
        }

        try {
            LevelModel::destroy($id); // unttuk menghapus data level

            return redirect('/level')->with('success', 'Data level berhasil dihapus');
        } catch (\Illuminate\Database\QueryException $e) {
            // Jika terjadi error ketika menghapus data, redirect kembali ke halaman dengan membawa pesan error
            return redirect('/level')->with('error', 'Data level gagal');
        }
    }

    public function confirm_ajax(string $id)
    {
        $level = LevelModel::find($id);

        return view('level.confirm_ajax', ['level' => $level]);
    }

    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            try {
                $level = LevelModel::find($id);
                if ($level) {
                    $level->delete();
                    return response()->json([
                        'status'  => true,
                        'message' => 'Data berhasil dihapus'
                    ]);
                } else {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Data tidak ditemukan'
                    ]);
                }
            } catch (\Exception $e) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ]);
            }
        }
        return redirect('/');
    }
}
