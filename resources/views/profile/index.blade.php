@extends('layouts.template')

@section('content')

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <!-- Foto Profil -->
            <img src="{{ asset('storage/profile/' . auth()->user()->foto) }}" 
                    alt="Foto Profil" 
                    class="img-circle" 
                    style="width: 200px; height: 200px; object-fit: cover;">
            
            <!-- Nama -->
            <h3 class="card-title mb-0 ml-3"> Halo {{ auth()->user()->nama }}</h3>
        </div>
        <div class="card-tools">
            <!-- Tombol atau tools lainnya -->
        </div>
    </div>

    <div class="card-body">

        <!-- Form Upload Foto Profil -->
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
        
            <div class="form-group">
                <label for="foto">Pilih Foto Profil</label>
                <input type="file" name="foto" class="form-control" id="foto">
            </div>
        
            <button type="submit" class="btn btn-primary">Simpan Foto</button>
        </form>

        @if(auth()->user()->foto)
            <form action="{{ route('profile.delete') }}" method="POST" class="mt-2">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Hapus Foto</button>
            </form>
        @endif

    </div>
</div>
@endsection


