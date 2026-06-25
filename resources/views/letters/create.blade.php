@extends('layouts.admin')

@section('content')
<div x-data="letterForm()" class="pb-10">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Kolom Kiri: Form -->
        <div class="lg:col-span-5">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Form Surat</h2>

            <form action="{{ route('letters.store') }}" method="POST" class="space-y-5">
                @csrf
                
                <!-- Jenis Surat -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Surat</label>
                    <select name="type_code" x-model="typeCode" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                        <option value="" disabled selected>Pilih Jenis Surat</option>
                        @foreach($letterTypes as $code => $name)
                            <option value="{{ $code }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Nomor Surat (Otomatis) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Surat</label>
                    <input type="text" name="letter_number" x-model="autoNumber" readonly required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm bg-gray-50 text-gray-500 focus:outline-none"
                        placeholder="Pilih jenis surat untuk memunculkan nomor">
                    <p class="text-xs text-gray-400 mt-1">Otomatis terisi berdasarkan jenis surat</p>
                </div>

                <!-- Nama Kegiatan -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kegiatan (Perihal)</label>
                    <input type="text" name="event_name" x-model="eventName"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Contoh : Rapat Wali Murid">
                </div>

                <!-- Tanggal Surat -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Surat</label>
                    <input type="date" name="letter_date" x-model="letterDate" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Tujuan -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tujuan</label>
                    <input type="text" name="destination" x-model="destination"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Contoh : Seluruh Guru">
                </div>

                <!-- Isi Surat -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Isi Surat</label>
                    <textarea name="content" x-model="content" rows="6" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Tulis paragraf isi surat di sini..."></textarea>
                </div>

                <!-- Pilihan Kepala Sekolah -->
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="block text-sm font-medium text-gray-700">Kepala Sekolah (Penandatangan)</label>
                        <button type="button" @click="showPejabatModal = true" class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                            + Tambah Pejabat Baru
                        </button>
                    </div>
                    <select name="kepsek_id" x-model="kepsekId" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                        <option value="" disabled selected>Pilih Kepala Sekolah</option>
                        <template x-for="pejabat in pejabats" :key="pejabat.id">
                            <option :value="pejabat.id" x-text="pejabat.nama"></option>
                        </template>
                    </select>
                </div>

                <!-- Jumlah Penandatangan -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Penandatangan</label>
                    <div class="flex gap-4">
                        <label class="flex items-center">
                            <input type="radio" name="jumlah_ttd" value="1" x-model="jumlahTtd" class="mr-2" required>
                            <span class="text-sm">1 Penandatangan (Kepala Sekolah)</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="jumlah_ttd" value="2" x-model="jumlahTtd" class="mr-2" required>
                            <span class="text-sm">2 Penandatangan (Pihak 1 & Kepsek)</span>
                        </label>
                    </div>
                </div>

                <div x-show="jumlahTtd == '2'" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pihak 1 / Guru Pendamping</label>
                    <select name="pihak1_id" x-model="pihak1Id"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white"
                        :required="jumlahTtd == '2'">
                        <option value="" disabled selected>Pilih Pihak 1</option>
                        <template x-for="guru in gurus" :key="guru.id">
                            <option :value="guru.id" x-text="guru.name"></option>
                        </template>
                    </select>
                </div>

                <div class="flex gap-4 pt-2">
                    <button type="submit" name="action" value="draft" class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                        Simpan Draft
                    </button>
                    <button type="submit" name="action" value="pending" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition">
                        Ajukan
                    </button>
                </div>
            </form>
        </div>

        <!-- Kolom Kanan: Preview -->
        <div class="lg:col-span-7">
            <h2 class="text-xl font-bold text-gray-900 mb-6 text-center">Preview Surat</h2>
            
            <!-- Kertas Preview A4 -->
            <div class="bg-white p-10 md:p-12 shadow-md border border-gray-200 rounded-lg mx-auto text-[13px] text-black w-full max-w-3xl" style="aspect-ratio: 1 / 1.414; font-family: 'Times New Roman', Times, serif;">
                
                <!-- KOP Surat -->
                <div class="flex items-center justify-between border-b-4 border-black pb-4 mb-6">
                    <img src="{{ asset('images/logo_yayasan.png') }}" alt="Logo Yayasan" class="w-20 h-20 object-contain flex-shrink-0">
                    <div class="text-center px-4">
                        <div class="font-bold text-sm tracking-wide">YAYASAN JAUHARUL HUDA AL-ALMANSHUIR</div>
                        <div class="font-bold text-lg tracking-wider">SEKOLAH MENENGAH ATAS</div>
                        <div class="font-bold text-xl tracking-widest">AL MANSHUR</div>
                        <div class="text-[11px] font-semibold">Terakreditasi "A"</div>
                        <div class="text-[10px]">Alamat: Jl. Kawali-Panjalu KM 07 Desa Sandingtaman Kec Panjalu Kab Ciamis Jawa Barat 46264</div>
                        <div class="text-[10px]">Tlp: 082217803253 email: <span class="text-blue-600">almanshurpublisher01@gmail.com</span></div>
                    </div>
                    <img src="{{ asset('images/logo_sekolah.png') }}" alt="Logo Sekolah" class="w-20 h-20 object-contain flex-shrink-0">
                </div>

                <!-- Nomor & Lampiran -->
                <div class="mb-6">
                    <table class="w-full">
                        <tr>
                            <td class="w-24 align-top">Nomor</td>
                            <td class="w-4 align-top">:</td>
                            <td><span x-text="autoNumber || '(nomor surat isi otomatis)'"></span></td>
                        </tr>
                        <tr>
                            <td class="align-top">Lampiran</td>
                            <td class="align-top">:</td>
                            <td>-</td>
                        </tr>
                        <tr>
                            <td class="align-top">Perihal</td>
                            <td class="align-top">:</td>
                            <td x-text="eventName || '(nama kegiatan isi otomatis)'"></td>
                        </tr>
                    </table>
                </div>

                <!-- Tujuan -->
                <div class="mb-6">
                    <div>Kepada Yth,</div>
                    <div x-text="destination || '(Tujuan isi otomatis)'"></div>
                    <div>di tempat</div>
                </div>

                <!-- Salam Pembuka -->
                <div class="text-center italic mb-4">
                    Assalamu'alaikum warahmatullahi wabarakatuh
                </div>

                <!-- Isi Surat -->
                <div class="text-justify leading-relaxed whitespace-pre-wrap min-h-[150px]" x-text="content || '(isi surat otomatis)'"></div>

                <!-- Salam Penutup -->
                <div class="text-center italic mt-6 mb-8">
                    Wassalamu'alaikum warahmatullahi wabarakatuh
                </div>

                <!-- Tanda Tangan -->
                <div class="mt-12 flex" :class="jumlahTtd == '2' ? 'justify-between' : 'justify-end'">
                    <!-- Pihak 1 -->
                    <div x-show="jumlahTtd == '2'" class="text-center w-64" style="display: none;">
                        <div class="h-6"></div> <!-- Spacer to match Kepsek's date -->
                        <div>Mengetahui,</div>
                        <div>Pihak 1 / Guru Pendamping</div>
                        <div class="font-bold">SMA AL MANSHUR</div>
                        
                        <div class="h-20"></div> <!-- Space for signature -->
                        
                        <div class="font-bold underline" x-text="pihak1Name || '(Nama Pihak 1)'"></div>
                    </div>

                    <!-- Kepala Sekolah -->
                    <div class="text-center w-64">
                        <div>Panjalu, <span x-text="formattedDate"></span></div>
                        <div>Mengetahui,</div>
                        <div>Kepala Sekolah</div>
                        <div class="font-bold">SMA AL MANSHUR</div>
                        
                        <div class="h-20"></div> <!-- Space for signature -->
                        
                        <div class="font-bold underline" x-text="kepsekName || '(Nama Kepala Sekolah)'"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Modal Tambah Pejabat -->
    <div x-show="showPejabatModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4" @click.away="showPejabatModal = false">
            <div class="flex justify-between items-center px-6 py-4 border-b">
                <h3 class="text-lg font-bold text-gray-900">Tambah Pejabat Baru</h3>
                <button type="button" @click="showPejabatModal = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap & Gelar</label>
                    <input type="text" x-model="newPejabat.nama" class="w-full px-4 py-2 border rounded-lg text-sm focus:ring-blue-500" placeholder="Contoh: Budi Santoso, S.Pd">
                </div>
            </div>
            <div class="px-6 py-4 border-t flex justify-end gap-3 bg-gray-50 rounded-b-lg">
                <button type="button" @click="showPejabatModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Batal</button>
                <button type="button" @click="savePejabat" :disabled="isLoadingPejabat" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-50 flex items-center">
                    <span x-show="isLoadingPejabat" class="mr-2">...</span>
                    Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function letterForm() {
        return {
            typeCode: '',
            sequenceData: @json($sequences),
            eventName: '',
            letterDate: '',
            destination: '',
            content: '',
            jumlahTtd: '1',
            pihak1Id: '',
            gurus: @json($gurus),
            kepsekId: '',
            pejabats: @json($pejabats),
            
            showPejabatModal: false,
            isLoadingPejabat: false,
            newPejabat: { nama: '' },

            init() {
                const active = this.pejabats.find(p => p.is_active);
                if (active) {
                    this.kepsekId = active.id;
                }
            },

            get pihak1Name() {
                if (!this.pihak1Id) return '';
                const g = this.gurus.find(g => g.id == this.pihak1Id);
                return g ? g.name : '';
            },

            get kepsekName() {
                if (!this.kepsekId) return '';
                const p = this.pejabats.find(p => p.id == this.kepsekId);
                return p ? p.nama : '';
            },

            get kepsekNip() {
                if (!this.kepsekId) return '';
                const p = this.pejabats.find(p => p.id == this.kepsekId);
                return p ? p.nip : '';
            },

            async savePejabat() {
                if (!this.newPejabat.nama) return alert('Nama wajib diisi');
                this.isLoadingPejabat = true;
                try {
                    const res = await fetch('{{ route('letters.pejabat.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(this.newPejabat)
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.pejabats.push(data.pejabat);
                        this.kepsekId = data.pejabat.id;
                        this.showPejabatModal = false;
                        this.newPejabat = { nama: '' };
                    }
                } catch (e) {
                    alert('Gagal menyimpan pejabat');
                }
                this.isLoadingPejabat = false;
            },
            
            get autoNumber() {
                if (!this.typeCode) return '';
                const romanMonths = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
                let date = this.letterDate ? new Date(this.letterDate) : new Date();
                let month = romanMonths[date.getMonth()];
                let year = date.getFullYear();
                let seq = this.sequenceData[this.typeCode] || '01';
                return `${this.typeCode}.${seq}/SMA-AM/${month}/${year}`;
            },
            
            get formattedDate() {
                if (!this.letterDate) return '(tanggal isi otomatis)';
                const options = { day: 'numeric', month: 'long', year: 'numeric' };
                return new Date(this.letterDate).toLocaleDateString('id-ID', options);
            }
        }
    }
</script>
@endsection
