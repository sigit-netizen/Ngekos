<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Onboarding | Ngekos.id</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }

        .glass {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }

        .bg-gradient {
            background: radial-gradient(circle at top right, #36B2B2, transparent), radial-gradient(circle at bottom left, #1D4ED8, transparent);
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen flex justify-center py-12 px-6 bg-gradient" x-data="{ showOtpModal: {{ session('otp_sent') ? 'true' : 'false' }} }">

    <!-- Floating Blobs -->
    <div class="absolute top-0 -left-4 w-72 h-72 bg-emerald-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
    <div class="absolute top-0 -right-4 w-72 h-72 bg-blue-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>
    <div class="absolute -bottom-8 left-20 w-72 h-72 bg-purple-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-4000"></div>

    <div class="max-w-md w-full relative">
        <!-- Dashboard Card -->
        <div class="glass border border-white/40 shadow-2xl rounded-3xl p-8 text-center">

            <!-- Progress Header -->
            <div class="flex items-center justify-between mb-8 px-4">
                <div class="flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold {{ !$pendingUser->nik ? 'bg-[#36B2B2] text-white' : 'bg-emerald-100 text-emerald-600' }}">1</div>
                    <span class="text-[9px] mt-1 font-bold text-gray-400 uppercase">Profil</span>
                </div>
                <div class="flex-1 h-px bg-gray-200 mx-4 {{ $pendingUser->nik ? 'bg-emerald-200' : '' }}"></div>
                <div class="flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold {{ ($pendingUser->nik && !$pendingUser->nomor_wa) || session('otp_sent') ? 'bg-[#36B2B2] text-white' : 'bg-gray-100 text-gray-400' }}">2</div>
                    <span class="text-[9px] mt-1 font-bold text-gray-400 uppercase">Verifikasi</span>
                </div>
            </div>

            @if($pendingUser->status === 'pending')
                
                @if(!$pendingUser->nik)
                    <!-- STEP 1: Basic Profile -->
                    <div class="mb-6">
                        <h1 class="text-2xl font-black text-gray-900 mb-2">Data Diri 📝</h1>
                        <p class="text-gray-500 text-sm">Lengkapi identitas Anda untuk keamanan bersama.</p>
                    </div>

                    <form action="{{ route('registration.step-one') }}" method="POST" class="text-left space-y-4" 
                        x-data="{ 
                            provinces: [], regencies: [], districts: [], villages: [],
                            selectedProv: '{{ old('provinsi_id') }}', 
                            selectedReg: '{{ old('kabupaten_id') }}', 
                            selectedDist: '{{ old('kecamatan_id') }}', 
                            selectedVill: '{{ old('desa_id') }}',
                            provName: '{{ old('provinsi_nama') }}', 
                            regName: '{{ old('kabupaten_nama') }}', 
                            distName: '{{ old('kecamatan_nama') }}', 
                            villName: '{{ old('desa_nama') }}',
                            loading: false,
                            async init() {
                                this.loading = true;
                                try {
                                    const res = await fetch('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json');
                                    this.provinces = await res.json();
                                    if(this.selectedProv) await this.fetchRegencies(true);
                                    if(this.selectedReg) await this.fetchDistricts(true);
                                    if(this.selectedDist) await this.fetchVillages(true);
                                } catch(e) { console.error('API Error', e); }
                                this.loading = false;
                            },
                            async fetchRegencies(isInit = false) {
                                if(!this.selectedProv) return;
                                const p = this.provinces.find(p => p.id === this.selectedProv);
                                if(p) this.provName = p.name;
                                
                                if(!isInit) {
                                    this.regName = ''; this.distName = ''; this.villName = '';
                                    this.selectedReg = ''; this.selectedDist = ''; this.selectedVill = '';
                                }
                                
                                this.loading = true;
                                const res = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${this.selectedProv}.json`);
                                this.regencies = await res.json();
                                this.loading = false;
                            },
                            async fetchDistricts(isInit = false) {
                                if(!this.selectedReg) return;
                                const r = this.regencies.find(r => r.id === this.selectedReg);
                                if(r) this.regName = r.name;

                                if(!isInit) {
                                    this.distName = ''; this.villName = '';
                                    this.selectedDist = ''; this.selectedVill = '';
                                }

                                this.loading = true;
                                const res = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/districts/${this.selectedReg}.json`);
                                this.districts = await res.json();
                                this.loading = false;
                            },
                            async fetchVillages(isInit = false) {
                                if(!this.selectedDist) return;
                                const d = this.districts.find(d => d.id === this.selectedDist);
                                if(d) this.distName = d.name;

                                if(!isInit) {
                                    this.villName = '';
                                    this.selectedVill = '';
                                }

                                this.loading = true;
                                const res = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/villages/${this.selectedDist}.json`);
                                this.villages = await res.json();
                                this.loading = false;
                            },
                            updateVillName() {
                                const v = this.villages.find(v => v.id === this.selectedVill);
                                if(v) this.villName = v.name;
                            }
                        }">
                        @csrf
                        
                        <!-- Hidden Name Inputs (Required by Controller) -->
                        <input type="hidden" name="provinsi_nama" :value="provName">
                        <input type="hidden" name="kabupaten_nama" :value="regName">
                        <input type="hidden" name="kecamatan_nama" :value="distName">
                        <input type="hidden" name="desa_nama" :value="villName">

                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 ml-2">NIK (KTP)</label>
                                <input type="number" name="nik" placeholder="16 digit NIK" required value="{{ old('nik') }}"
                                    class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm focus:border-[#36B2B2] focus:ring-2 focus:ring-[#36B2B2]/10 transition-all @error('nik') border-red-500 @enderror">
                                @error('nik') <p class="text-[10px] text-red-500 mt-1 ml-2">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 ml-2">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" required value="{{ old('tanggal_lahir') }}"
                                    class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm focus:border-[#36B2B2] focus:ring-2 focus:ring-[#36B2B2]/10 transition-all @error('tanggal_lahir') border-red-500 @enderror">
                                @error('tanggal_lahir') <p class="text-[10px] text-red-500 mt-1 ml-2">{{ $message }}</p> @enderror
                            </div>
                            
                            <!-- Regional Dropdowns -->
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 ml-2">Provinsi</label>
                                    <select name="provinsi_id" x-model="selectedProv" @change="fetchRegencies()" required
                                        class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-xs focus:border-[#36B2B2] focus:ring-2 focus:ring-[#36B2B2]/10 @error('provinsi_nama') border-red-500 @enderror">
                                        <option value="">Pilih</option>
                                        <template x-for="p in provinces" :key="p.id">
                                            <option :value="p.id" x-text="p.name" :selected="p.id == selectedProv"></option>
                                        </template>
                                    </select>
                                    @error('provinsi_nama') <p class="text-[9px] text-red-500 mt-1 ml-2">Pilih Provinsi</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 ml-2">Kota/Kab</label>
                                    <select name="kabupaten_id" x-model="selectedReg" @change="fetchDistricts()" required :disabled="!selectedProv"
                                        class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-xs focus:border-[#36B2B2] focus:ring-2 focus:ring-[#36B2B2]/10 disabled:opacity-50 @error('kabupaten_nama') border-red-500 @enderror">
                                        <option value="">Pilih</option>
                                        <template x-for="r in regencies" :key="r.id">
                                            <option :value="r.id" x-text="r.name" :selected="r.id == selectedReg"></option>
                                        </template>
                                    </select>
                                    @error('kabupaten_nama') <p class="text-[9px] text-red-500 mt-1 ml-2">Pilih Kota</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 ml-2">Kecamatan</label>
                                    <select name="kecamatan_id" x-model="selectedDist" @change="fetchVillages()" required :disabled="!selectedReg"
                                        class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-xs focus:border-[#36B2B2] focus:ring-2 focus:ring-[#36B2B2]/10 disabled:opacity-50 @error('kecamatan_nama') border-red-500 @enderror">
                                        <option value="">Pilih</option>
                                        <template x-for="d in districts" :key="d.id">
                                            <option :value="d.id" x-text="d.name" :selected="d.id == selectedDist"></option>
                                        </template>
                                    </select>
                                    @error('kecamatan_nama') <p class="text-[9px] text-red-500 mt-1 ml-2">Pilih Kec.</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 ml-2">Desa/Kel</label>
                                    <select name="desa_id" x-model="selectedVill" @change="updateVillName()" required :disabled="!selectedDist"
                                        class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-xs focus:border-[#36B2B2] focus:ring-2 focus:ring-[#36B2B2]/10 disabled:opacity-50 @error('desa_nama') border-red-500 @enderror">
                                        <option value="">Pilih</option>
                                        <template x-for="v in villages" :key="v.id">
                                            <option :value="v.id" x-text="v.name" :selected="v.id == selectedVill"></option>
                                        </template>
                                    </select>
                                    @error('desa_nama') <p class="text-[9px] text-red-500 mt-1 ml-2">Pilih Desa</p> @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 ml-2">Detail Alamat (Jalan/No. Rumah)</label>
                                <textarea name="alamat_detail" rows="1" placeholder="Jl. Merdeka No. 10..." required
                                    class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm focus:border-[#36B2B2] focus:ring-2 focus:ring-[#36B2B2]/10 transition-all @error('alamat_detail') border-red-500 @enderror">{{ old('alamat_detail') }}</textarea>
                                @error('alamat_detail') <p class="text-[10px] text-red-500 mt-1 ml-2">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <button type="submit" class="w-full py-4 bg-[#36B2B2] text-white rounded-2xl font-black shadow-lg shadow-[#36B2B2]/30 hover:bg-[#2D8E8E] transition-all transform active:scale-95" :class="loading ? 'opacity-50 cursor-not-allowed' : ''" :disabled="loading">
                            <span x-show="!loading">LANJUT KE STEP 2</span>
                            <span x-show="loading" style="display: none;">MOHON TUNGGU...</span>
                        </button>
                    </form>

                @elseif(!$pendingUser->nomor_wa || session('otp_sent'))
                    <!-- STEP 2: WA & Plan -->
                    <div class="mb-6">
                        <h1 class="text-2xl font-black text-gray-900 mb-2">Verifikasi WA 📱</h1>
                        <p class="text-gray-500 text-sm">Validasi nomor Anda untuk menerima notifikasi.</p>
                    </div>

                    <form action="{{ route('registration.send-otp') }}" method="POST" class="text-left space-y-4"
                        x-data="{ 
                            selectedPlan: '{{ old('plan_type', $pendingUser->plan_type) }}',
                            get isPerKamar() {
                                return this.selectedPlan.toLowerCase().includes('kamar');
                            }
                        }">
                        @csrf
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 ml-2">Nomor WhatsApp</label>
                            <input type="number" name="nomor_wa" placeholder="08..." required value="{{ old('nomor_wa', $pendingUser->nomor_wa) }}"
                                class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm focus:border-[#36B2B2] focus:ring-2 focus:ring-[#36B2B2]/10 transition-all">
                        </div>

                        @if($pendingUser->id_plans == 2)
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 ml-2">Pilih Paket Pemilik</label>
                            <select name="plan_type" x-model="selectedPlan" required
                                class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm focus:border-[#36B2B2] focus:ring-2 focus:ring-[#36B2B2]/10 transition-all">
                                <option value="">Pilih Paket...</option>
                                @foreach($plans as $plan)
                                    <option value="{{ $plan->nama_plans }}">{{ $plan->nama_plans }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Conditional Room Count -->
                        <div x-show="isPerKamar" x-transition x-cloak>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 ml-2">Jumlah Kamar</label>
                            <input type="number" name="jumlah_kamar" min="1" placeholder="Masukkan jumlah kamar..." 
                                :required="isPerKamar"
                                value="{{ old('jumlah_kamar', $pendingUser->jumlah_kamar ?? 1) }}"
                                class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm focus:border-[#36B2B2] focus:ring-2 focus:ring-[#36B2B2]/10 transition-all">
                            <p class="text-[10px] text-gray-400 mt-1 ml-2">Minimal 1 kamar.</p>
                        </div>
                        @else
                            <input type="hidden" name="plan_type" value="Member">
                        @endif

                        <button type="submit" class="w-full py-4 bg-[#36B2B2] text-white rounded-2xl font-black shadow-lg shadow-[#36B2B2]/30 hover:bg-[#2D8E8E] transition-all transform active:scale-95">
                            KIRIM KODE OTP
                        </button>
                    </form>

                @else
                    <!-- FINISHED ONBOARDING, WAITING FOR ADMIN -->
                    <div class="relative w-24 h-24 mx-auto mb-6">
                        <div class="absolute inset-0 bg-emerald-500 rounded-full animate-ping opacity-20"></div>
                        <div class="relative bg-white rounded-full p-5 shadow-xl flex items-center justify-center">
                            <svg class="w-12 h-12 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                    </div>
                    <h1 class="text-2xl font-black text-gray-900 mb-2">Verifikasi Data ⏳</h1>
                    <p class="text-gray-500 text-sm mb-6">Terima kasih, <span class="text-emerald-600 font-bold">{{ $pendingUser->name }}</span>! Data Anda sedang kami tinjau. Mohon tunggu kabar dari kami melalui WhatsApp.</p>
                    
                    <div class="inline-flex items-center px-4 py-2 bg-emerald-50 text-emerald-700 rounded-full text-[10px] font-black border border-emerald-100 uppercase tracking-widest mb-8">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2"></span>
                        Status: Menunggu Persetujuan Admin
                    </div>
                @endif

            @elseif($pendingUser->status === 'verified')
                <!-- DISINI BAGIAN PAYMENT YANG DIRESTORE -->
                <div class="relative w-24 h-24 mx-auto mb-6">
                    <div class="absolute inset-0 bg-blue-500 rounded-full animate-ping opacity-20"></div>
                    <div class="relative bg-white rounded-full p-5 shadow-xl flex items-center justify-center">
                        <svg class="w-12 h-12 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                </div>

                <h1 class="text-2xl font-black text-gray-900 mb-2">Data Disetujui! ✅</h1>
                <p class="text-gray-500 text-sm mb-6">Data Anda telah diverifikasi. Untuk mengaktifkan akun Pemilik Kos, silakan lakukan pembayaran pendaftaran.</p>

                <div class="bg-gray-50 rounded-2xl p-5 mb-6 text-left border border-gray-100 italic text-[10px]">
                    <p class="mb-2 font-black text-gray-400 uppercase tracking-widest underline underline-offset-4 decoration-2 decoration-emerald-200">Info Pembayaran:</p>
                    <div class="mb-3">
                        <p class="text-[9px] text-gray-400 uppercase font-black mb-1">Total Yang Harus Dibayar:</p>
                        <p class="text-xl font-black text-[#36B2B2]">Rp {{ number_format($totalPembayaran, 0, ',', '.') }}</p>
                        <p class="text-[9px] text-gray-500 mt-1">
                            Paket: <span class="font-bold text-gray-700">{{ $pendingUser->plan_type }}</span>
                            @if(str_contains(strtolower($pendingUser->plan_type), 'kamar'))
                                ({{ $pendingUser->jumlah_kamar }} Kamar)
                            @endif
                        </p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-gray-900 font-bold">Bank BCA: 1234567890</p>
                        <p class="text-gray-900 font-bold">A/N: PT NGEKOS INDONESIA</p>
                    </div>
                </div>

                <form action="{{ route('registration.upload-proof') }}" method="POST" enctype="multipart/form-data" class="mb-6 text-left">
                    @csrf
                    <input type="hidden" name="email" value="{{ $pendingUser->email }}">
                    <input type="hidden" name="metode_pembayaran" value="Transfer Bank">

                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-2">Upload Bukti (Foto/Gambar)</label>
                    <input type="file" name="bukti_pembayaran" accept="image/*" required class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-xs">
                    <button type="submit" class="w-full mt-4 py-4 bg-[#36B2B2] text-white rounded-2xl font-black shadow-lg">AKTIFKAN SEKARANG</button>
                </form>

            @elseif($pendingUser->status === 'konfirmasi')
                <!-- DISINI BAGIAN KONFIRMASI YANG DIRESTORE -->
                <div class="relative w-24 h-24 mx-auto mb-6">
                    <div class="absolute inset-0 bg-amber-500 rounded-full animate-ping opacity-20"></div>
                    <div class="relative bg-white rounded-full p-5 shadow-xl flex items-center justify-center">
                        <svg class="w-12 h-12 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                </div>
                <h1 class="text-2xl font-black text-gray-900 mb-2 tracking-tight">Menunggu Verifikasi Pembayaran ⏳</h1>
                <p class="text-gray-500 text-sm mb-6">Bukti pembayaran sedang dicek. Akun aktif otomatis setelah divalidasi.</p>
                <div class="inline-flex items-center px-4 py-2 bg-amber-50 text-amber-800 rounded-full text-[10px] font-black border border-amber-100 uppercase tracking-widest mb-8">Status: Proses Validasi Keuangan</div>
            @endif

            <!-- Common Action -->
            <div class="pt-6 border-t border-gray-100 mt-6 space-y-3">
                <a href="{{ route('home') }}" class="block w-full py-3 text-xs font-bold text-gray-400 hover:text-gray-600 transition-colors uppercase tracking-widest">Kembali Ke Beranda</a>
            </div>
        </div>
    </div>

    <!-- OTP MODAL -->
    <div x-show="showOtpModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100" class="fixed inset-0 z-50 flex items-center justify-center p-6 bg-black/50 backdrop-blur-sm" style="display: none;">
        <div class="bg-white rounded-3xl p-8 max-w-xs w-full text-center shadow-2xl relative overflow-hidden">
             <!-- Background pattern -->
            <div class="absolute -top-12 -right-12 w-32 h-32 bg-emerald-50 rounded-full"></div>
            
            <h2 class="text-xl font-black text-gray-900 mb-2 relative">Verifikasi OTP</h2>
            <p class="text-gray-400 text-xs mb-6 relative">Masukkan 6 digit kode yang kami kirim ke WhatsApp Anda.</p>
            
            <form action="{{ route('registration.verify-otp') }}" method="POST" class="space-y-6 relative">
                @csrf
                <input type="number" name="otp" placeholder="......" required autofocus
                    class="w-full text-center text-3xl font-black tracking-[0.5em] py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:border-[#36B2B2] focus:ring-0 transition-all placeholder-gray-200">
                
                <button type="submit" class="w-full py-4 bg-[#36B2B2] text-white rounded-2xl font-black shadow-lg shadow-[#36B2B2]/30 hover:bg-[#2D8E8E] transition-all transform active:scale-95">
                    VERIFIKASI SEKARANG
                </button>
            </form>
            
            <button @click="showOtpModal = false" class="mt-6 text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-gray-600">Batal/Ubah Nomor</button>
        </div>
    </div>

    @if(session('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" class="fixed bottom-6 right-6 bg-emerald-500 text-white px-6 py-4 rounded-2xl shadow-2xl font-bold flex items-center z-[100]">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" class="fixed bottom-6 right-6 bg-rose-500 text-white px-6 py-4 rounded-2xl shadow-2xl font-bold flex items-center z-[100]">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('error') }}
        </div>
    @endif

    <style>
        @keyframes blob { 0% { transform: translate(0px, 0px) scale(1); } 33% { transform: translate(30px, -50px) scale(1.1); } 66% { transform: translate(-20px, 20px) scale(0.9); } 100% { transform: translate(0px, 0px) scale(1); } }
        .animate-blob { animation: blob 7s infinite; }
        .animation-delay-2000 { animation-delay: 2s; }
        .animation-delay-4000 { animation-delay: 4s; }
    </style>
</body>
</html>