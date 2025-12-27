@extends('layouts.main')
@section('title', 'Edit Profil')

@section('content')
<div class="profile-container">
    <h1 class="page-title">Edit Biodata</h1>

    <div class="form-card">
        {{-- Bagian Foto Profil (Sama seperti halaman profil) --}}
        <div class="profile-picture-section">
            <form action="{{ route('profil.photo.update') }}" method="POST" enctype="multipart/form-data" id="photoForm">
                @csrf
                <div class="picture-placeholder">
                    @if($user->profile_photo_path)
                        <img src="{{ Storage::url($user->profile_photo_path) }}" alt="Foto Profil" id="photoPreview">
                    @else
                        <i class="fas fa-user default-icon"></i>
                    @endif
                    <label for="profile_photo" class="edit-picture-btn" title="Ubah Foto Profil">
                        <i class="fas fa-camera"></i>
                    </label>
                </div>
                <input type="file" id="profile_photo" name="profile_photo" style="display: none;" accept="image/*">
                {{-- Tombol ini akan muncul setelah gambar dipilih --}}
                <div class="form-actions photo-actions" id="photoActions" style="display: none;">
                    <button type="submit" class="btn-primary-small">Simpan Foto</button>
                </div>
            </form>
            @error('profile_photo') <span class="error-msg photo-error">{{ $message }}</span> @enderror
            @if(session('success')) <span class="success-msg photo-success">{{ session('success') }}</span> @endif
        </div>

        {{-- Form Edit Biodata --}}
        <form action="{{ route('profil.update') }}" method="POST" class="profile-details-form">
            @csrf
            @method('PUT')

            <div class="detail-item">
                <label for="name">Nama Lengkap</label>
                <input type="text" name="name" id="name" class="form-control-clean" value="{{ old('name', $user->name) }}" required>
                @error('name') <span class="error-msg">{{ $message }}</span> @enderror
            </div>

            <div class="detail-item">
                <label for="tanggal_lahir">Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control-clean" value="{{ old('tanggal_lahir', $user->tanggal_lahir) }}">
                @error('tanggal_lahir') <span class="error-msg">{{ $message }}</span> @enderror
            </div>

            <div class="detail-item">
                <label for="alamat">Alamat</label>
                <input type="text" name="alamat" id="alamat" class="form-control-clean" value="{{ old('alamat', $user->alamat) }}">
                @error('alamat') <span class="error-msg">{{ $message }}</span> @enderror
            </div>
            
            <div class="detail-item">
                <label for="no_hp">No Telepon/WhatsApp</label>
                <input type="tel" name="no_hp" id="no_hp" class="form-control-clean" value="{{ old('no_hp', $user->no_hp) }}">
                @error('no_hp') <span class="error-msg">{{ $message }}</span> @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">Simpan Perubahan</button>
                <a href="{{ route('profil') }}" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
{{-- Tambahan style untuk tombol simpan foto --}}
<style>
    .photo-actions {
        margin-top: 15px;
        text-align: center;
    }
    .btn-primary-small {
        background-color: #007e6c;
        color: #fff;
        border: none;
        padding: 8px 18px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 500;
        text-decoration: none;
        transition: background-color .3s ease;
        font-size: 0.9rem;
    }
    .btn-primary-small:hover { background-color: #009d83; }
</style>
<style>
    .profile-container { padding: 30px 40px; max-width: 700px; margin: 20px auto; }
    .page-title { color: #007e6c; margin-bottom: 25px; font-weight: 600; text-align: center; }
    .form-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 5px 20px rgba(0, 95, 82, 0.08);
        border: 1px solid #eef3f7;
        padding: 30px 40px; /* Padding ditambah */
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    /* --- Style Foto Profil (Disalin dari profil.blade.php) --- */
    .profile-picture-section { margin-bottom: 30px; position: relative; }
    .picture-placeholder {
        width: 150px; height: 150px; border-radius: 50%;
        background-color: #e9ecef; display: flex;
        justify-content: center; align-items: center;
        overflow: hidden; border: 4px solid #fff;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .picture-placeholder img { width: 100%; height: 100%; object-fit: cover; }
    .picture-placeholder .default-icon { font-size: 60px; color: #adb5bd; }
    .edit-picture-btn {
        position: absolute; bottom: 5px; right: 5px;
        background-color: #007e6c; color: white;
        border: 2px solid white; border-radius: 50%;
        width: 35px; height: 35px; display: flex;
        justify-content: center; align-items: center;
        font-size: 16px; cursor: pointer; transition: background-color 0.2s;
    }
    .edit-picture-btn:hover { background-color: #005f52; }
    .photo-error, .photo-success { display: block; text-align: center; margin-top: 10px; font-size: 0.9rem; }
    .photo-error { color: #dc3545; }
    .photo-success { color: #198754; }

    /* --- Style Form (Baru) --- */
    .profile-details-form { width: 100%; }
    .detail-item {
        width: 100%;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid #f0f3f6; /* Garis pemisah */
    }
    .detail-item:last-of-type { border-bottom: none; }
    .detail-item label {
        display: block; font-size: 0.9rem;
        color: #7f8c8d; margin-bottom: 5px; font-weight: 500;
    }
    /* Input field dibuat transparan agar mirip teks */
    .form-control-clean {
        width: 100%;
        border: none;
        padding: 4px 0;
        font-size: 1.1rem;
        color: #34495e;
        font-weight: 500;
        font-family: 'Poppins', sans-serif;
        background-color: transparent;
        border-radius: 0; /* Hapus radius */
        border-bottom: 2px solid transparent; /* Garis bawah transparan */
        transition: border-color 0.2s ease;
    }
    /* Efek focus */
    .form-control-clean:focus {
        outline: none;
        border-bottom-color: #007e6c; /* Garis bawah hijau saat fokus */
    }
    /* Menyamakan tampilan input date */
    input[type="date"].form-control-clean {
        padding: 2px 0;
    }

    .error-msg { color: #dc3545; font-size: 0.875em; margin-top: 5px; display: block; }

    /* Tombol */
    .form-actions { margin-top: 30px; text-align: center; }
    .btn-primary { background-color: #007e6c; color: #fff; border: none; padding: 12px 28px; border-radius: 8px; cursor: pointer; font-weight: 600; text-decoration: none; transition: background-color .3s ease; font-size: 1rem; }
    .btn-primary:hover { background-color: #009d83; }
    .btn-secondary { margin-left: 15px; color: #555; text-decoration: none; font-weight: 500; }
    .btn-secondary:hover { color: #007e6c; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const photoInput = document.getElementById('profile_photo');
    const photoActions = document.getElementById('photoActions');
    const placeholder = document.querySelector('.picture-placeholder');

    photoInput.addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            const reader = new FileReader();

            reader.onload = function(event) {
                // Cari elemen gambar yang ada dengan ID 'photoPreview'
                let imgPreview = document.getElementById('photoPreview');

                // Jika elemen gambar tidak ada (kasus pengguna belum punya foto)
                if (!imgPreview) {
                    const icon = placeholder.querySelector('.default-icon');
                    if (icon) icon.style.display = 'none'; // Sembunyikan ikon

                    // Buat elemen gambar baru
                    imgPreview = document.createElement('img');
                    imgPreview.id = 'photoPreview';
                    imgPreview.alt = 'Foto Profil';
                    placeholder.appendChild(imgPreview); // Tambahkan ke dalam placeholder
                }

                // Atur sumber gambar dari file yang dipilih
                imgPreview.src = event.target.result;
                // Tampilkan tombol simpan
                if (photoActions) photoActions.style.display = 'block';
            }

            reader.readAsDataURL(e.target.files[0]);
        }
    });
});
</script>
@endpush