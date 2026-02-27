@extends('layouts.dashboard')

@section('dashboard-content')
    <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-6 shadow-sm border border-white/50 mb-8" data-aos="fade-up">
        <div class="flex flex-col gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1">Data User 👤</h1>
                <p class="text-gray-500 text-sm">Kelola dan pantau seluruh penyewa (Anak Kos) yang terdaftar di platform.
                </p>
            </div>
            <div>
                <form action="{{ route('superadmin.data_user') }}" method="GET" class="flex items-center gap-2">
                    <input type="text" name="search" value="{{ $search ?? '' }}"
                        placeholder="Cari nama, email, NIK, atau WA..."
                        style="width:280px; padding: 10px 16px; font-size:14px; border: 2px solid #d1d5db; border-radius: 12px; outline:none; background:#fff; color:#1f2937;">
                    <button type="submit"
                        style="padding: 10px 20px; background-color: #36B2B2; color: white; font-size:14px; font-weight:700; border-radius:12px; cursor:pointer; border:none; white-space:nowrap;">
                        Cari
                    </button>
                    @if($search)
                        <a href="{{ route('superadmin.data_user') }}"
                            style="padding: 10px 16px; background-color: #f3f4f6; color: #4b5563; font-size:14px; font-weight:700; border-radius:12px; text-decoration:none; white-space:nowrap;">
                            Reset
                        </a>
                    @endif
                </form>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-100 text-green-600 rounded-xl font-bold text-sm"
            data-aos="fade-up">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden" data-aos="fade-up"
        data-aos-delay="100">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                        <th class="px-6 py-4">Nama & Email</th>
                        <th class="px-6 py-4">NIK & WA</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Tgl Daftar</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900">{{ $user->name }}</div>
                                <div class="text-xs text-gray-500">{{ $user->email }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-700">NIK: {{ $user->nik }}</div>
                                <div class="text-xs text-cyan-600 font-bold">WA: {{ $user->nomor_wa }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 bg-cyan-50 text-cyan-600 rounded-full text-[10px] font-black uppercase">
                                    {{ $user->getPlanName() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $user->created_at->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button onclick="editUser({{ json_encode($user) }})"
                                        class="p-2 text-amber-500 hover:bg-amber-50 rounded-lg transition-colors" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                    </button>
                                    <form action="{{ route('superadmin.data_user.destroy', $user->id) }}" method="POST"
                                        onsubmit="return confirm('Hapus user ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400 italic">Belum ada user terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>


    <!-- Modal Edit User -->
    <div id="editUserModal"
        style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); backdrop-filter:blur(4px); z-index:50; align-items:center; justify-content:center; padding:16px;">
        <div
            style="background:#fff; border-radius:24px; width:100%; max-width:520px; overflow:hidden; box-shadow:0 25px 50px -12px rgba(0,0,0,0.25); margin:auto;">

            {{-- Modal Header --}}
            <div
                style="padding:24px 28px; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; justify-content:space-between;">
                <div>
                    <h3 style="font-size:18px; font-weight:800; color:#0f172a; margin:0;">Edit User</h3>
                    <p style="font-size:12px; color:#94a3b8; margin:4px 0 0 0;">Perbarui data penyewa (Anak Kos)</p>
                </div>
                <button onclick="closeEditUserModal()"
                    style="background:#f8fafc; border:none; cursor:pointer; border-radius:10px; padding:8px; display:flex; align-items:center; justify-content:center;">
                    <svg width="20" height="20" fill="none" stroke="#64748b" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Modal Body --}}
            <form id="editForm" method="POST" style="padding:24px 28px;">
                @csrf
                @method('PUT')

                {{-- Row 1: Nama --}}
                <div style="margin-bottom:16px;">
                    <label
                        style="display:block; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:6px;">Nama
                        Lengkap</label>
                    <input type="text" name="name" id="edit_name" required
                        style="width:100%; padding:10px 14px; border:2px solid #e2e8f0; border-radius:12px; font-size:14px; color:#1e293b; background:#f8fafc; outline:none; box-sizing:border-box;"
                        onfocus="this.style.borderColor='#36B2B2'" onblur="this.style.borderColor='#e2e8f0'">
                </div>

                {{-- Row 2: Email + Password --}}
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:16px;">
                    <div>
                        <label
                            style="display:block; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:6px;">Email</label>
                        <input type="email" name="email" id="edit_email" required
                            style="width:100%; padding:10px 14px; border:2px solid #e2e8f0; border-radius:12px; font-size:14px; color:#1e293b; background:#f8fafc; outline:none; box-sizing:border-box;"
                            onfocus="this.style.borderColor='#36B2B2'" onblur="this.style.borderColor='#e2e8f0'">
                    </div>
                    <div>
                        <label
                            style="display:block; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:6px;">Password
                            <span style="color:#cbd5e1; font-weight:400;">(kosongkan jika tidak ganti)</span></label>
                        <input type="password" name="password"
                            style="width:100%; padding:10px 14px; border:2px solid #e2e8f0; border-radius:12px; font-size:14px; color:#1e293b; background:#f8fafc; outline:none; box-sizing:border-box;"
                            onfocus="this.style.borderColor='#36B2B2'" onblur="this.style.borderColor='#e2e8f0'">
                    </div>
                </div>

                {{-- Row 3: NIK + WA --}}
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:24px;">
                    <div>
                        <label
                            style="display:block; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:6px;">NIK</label>
                        <input type="text" name="nik" id="edit_nik" required
                            style="width:100%; padding:10px 14px; border:2px solid #e2e8f0; border-radius:12px; font-size:14px; color:#1e293b; background:#f8fafc; outline:none; box-sizing:border-box;"
                            onfocus="this.style.borderColor='#36B2B2'" onblur="this.style.borderColor='#e2e8f0'">
                    </div>
                    <div>
                        <label
                            style="display:block; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:6px;">Nomor
                            WA</label>
                        <input type="text" name="nomor_wa" id="edit_wa" required
                            style="width:100%; padding:10px 14px; border:2px solid #e2e8f0; border-radius:12px; font-size:14px; color:#1e293b; background:#f8fafc; outline:none; box-sizing:border-box;"
                            onfocus="this.style.borderColor='#36B2B2'" onblur="this.style.borderColor='#e2e8f0'">
                    </div>
                </div>

                {{-- Submit --}}
                <button type="submit"
                    style="width:100%; padding:13px; background-color:#36B2B2; color:white; font-size:15px; font-weight:800; border-radius:14px; border:none; cursor:pointer; letter-spacing:0.5px;"
                    onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                    Simpan Perubahan
                </button>
            </form>
        </div>
    </div>

    <script>
        function closeEditUserModal() {
            document.getElementById('editUserModal').style.display = 'none';
        }

        function editUser(user) {
            document.getElementById('editForm').action = `/superadmin/data-user/${user.id}`;
            document.getElementById('edit_name').value = user.name;
            document.getElementById('edit_email').value = user.email;
            document.getElementById('edit_nik').value = user.nik;
            document.getElementById('edit_wa').value = user.nomor_wa;
            document.getElementById('editUserModal').style.display = 'flex';
        }

        // Close on backdrop click
        document.getElementById('editUserModal').addEventListener('click', function (e) {
            if (e.target === this) closeEditUserModal();
        });
    </script>
@endsection