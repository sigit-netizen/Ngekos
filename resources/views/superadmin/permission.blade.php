@extends('layouts.dashboard')

@section('dashboard-content')
    <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-6 shadow-sm border border-white/50 mb-8" data-aos="fade-up">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Sistem Akses & Permission 🔑</h1>
        <p class="text-gray-500">Atur hak akses dan konfigurasi tingkat administrator menggunakan Spatie Roles &
            Permissions.</p>
    </div>

    <div class="bg-white rounded-2xl p-8 text-center border border-gray-100 shadow-sm" data-aos="fade-up"
        data-aos-delay="100">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-purple-50 mb-4">
            <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path>
            </svg>
        </div>
        <h3 class="text-lg font-bold text-gray-900 mb-6">Manajemen Akses Menu (Subscription)</h3>

        <!-- Form Tambah Permission Baru -->
        <div class="mb-8 p-5 bg-gray-50 border border-gray-100 rounded-xl">
            <h4 class="text-sm font-semibold text-gray-700 mb-3">Tambah Akses Menu dan Buat File Halaman Baru</h4>
            <form action="{{ route('superadmin.permission.store') }}" method="POST" class="flex flex-col sm:flex-row gap-3">
                @csrf
                <div class="flex-[2]">
                    <input type="text" name="name" placeholder="Nama Menu Baru (Contoh: edit_pengguna)" required
                        class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:outline-none focus:border-[#36B2B2] focus:ring-1 focus:ring-[#36B2B2] transition duration-200">
                    @error('name')
                        <p class="mt-2 text-xs text-red-500 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex-1">
                    <select name="kategori" required
                        class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:outline-none focus:border-[#36B2B2] focus:ring-1 focus:ring-[#36B2B2] transition duration-200 bg-white">
                        <option value="" disabled selected>Pilih Kategori Folder...</option>
                        <option value="admin">Admin (views/member)</option>
                        <option value="user">User (views/user)</option>
                    </select>
                    @error('kategori')
                        <p class="mt-2 text-xs text-red-500 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="bg-[#36B2B2] hover:bg-[#2b8f8f] text-white px-6 py-3 rounded-xl text-sm font-semibold transition-colors duration-300 shadow-sm h-fit whitespace-nowrap">
                    + Buat File & Akses
                </button>
            </form>
        </div>

        <!-- Header Filter (Tabs 2 Kategori Utama) -->
        <div class="flex flex-col sm:flex-row items-center justify-between mb-6 gap-4 border-b border-gray-100 pb-4">
            <h4 class="text-md font-bold text-gray-800">Daftar Matriks Akses</h4>

            <div class="flex bg-gray-100 p-1 rounded-xl">
                <!-- Tab Kategori Admin (Pro, Premium) -->
                <a href="{{ route('superadmin.permission', ['view_group' => 'admin']) }}" class="{{ $viewGroup === 'admin' ? 'bg-white shadow-sm text-[#36B2B2] font-semibold' : 'text-gray-500 hover:text-gray-700 font-medium' }} 
                                                  px-6 py-2.5 rounded-lg text-sm transition-all duration-300">
                    Administrator (Pro / Premium)
                </a>

                <!-- Tab Kategori User -->
                <a href="{{ route('superadmin.permission', ['view_group' => 'user']) }}" class="{{ $viewGroup === 'user' ? 'bg-white shadow-sm text-[#36B2B2] font-semibold' : 'text-gray-500 hover:text-gray-700 font-medium' }} 
                                                  px-6 py-2.5 rounded-lg text-sm transition-all duration-300">
                    Aplikasi User
                </a>
            </div>
        </div>

        <form action="{{ route('superadmin.permission.update') }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Hidden input untuk mengembalikan user ke tab yang sama setelah update sukses -->
            <input type="hidden" name="active_filter" value="{{ $viewGroup }}">

            <div class="overflow-x-auto text-left shadow-sm rounded-xl border border-gray-100">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="py-3 px-4 font-semibold text-gray-600">Menu Akses</th>
                            @foreach($roles as $r)
                                <th class="py-3 px-4 font-semibold text-gray-600 text-center capitalize">{{ $r->name }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($permissions as $permission)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="py-3 px-4 text-gray-700 font-medium tracking-wide flex items-center justify-between">
                                    <span>{{ str_replace('_', ' ', Str::title(str_replace('menu.', '', $permission->name))) }}</span>
                                    <button type="submit" form="delete-form-{{ $permission->id }}"
                                        class="text-red-500 hover:text-red-700 p-1.5 rounded-md hover:bg-red-50 transition-colors"
                                        title="Hapus Akses & Hapus File">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                    </button>
                                </td>
                                @foreach($roles as $r)
                                    <td class="py-3 px-4 text-center">
                                        <label class="toggle-switch">
                                            <input type="checkbox" name="role_permissions[{{ $r->id }}][]"
                                                value="{{ $permission->name }}"
                                                @checked($r->permissions->contains('name', $permission->name))>
                                            <span class="toggle-slider"></span>
                                        </label>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit"
                    class="bg-[#36B2B2] hover:bg-[#2b8f8f] text-white font-bold py-2.5 px-6 rounded-xl shadow-sm transition-all duration-300">
                    Simpan Perubahan Akses
                </button>
            </div>
        </form>

        <!-- Hidden Delete Forms for Permissions -->
        @foreach($permissions as $permission)
            <form id="delete-form-{{ $permission->id }}" action="{{ route('superadmin.permission.destroy', $permission->id) }}"
                method="POST" class="hidden"
                onsubmit="return confirm('Apakah Anda yakin ingin menghapus akses {{ $permission->name }} sepenuhnya? File views/ halamannya (jika ada) juga akan dihapus dan tidak dapat dikembalikan.');">
                @csrf
                @method('DELETE')
            </form>
        @endforeach
    </div>

    <style>
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 40px;
            height: 22px;
            cursor: pointer;
        }
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .toggle-slider {
            position: absolute;
            inset: 0;
            background-color: #e5e7eb;
            border-radius: 999px;
            transition: all 0.25s ease;
        }
        .toggle-slider::before {
            content: "";
            position: absolute;
            height: 16px;
            width: 16px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            border-radius: 50%;
            box-shadow: 0 1px 3px rgba(0,0,0,0.15);
            transition: all 0.25s ease;
        }
        .toggle-switch input:checked + .toggle-slider {
            background-color: #36B2B2;
        }
        .toggle-switch input:checked + .toggle-slider::before {
            transform: translateX(18px);
        }
        .toggle-switch input:focus + .toggle-slider {
            box-shadow: 0 0 0 3px rgba(54, 178, 178, 0.15);
        }
    </style>
@endsection
