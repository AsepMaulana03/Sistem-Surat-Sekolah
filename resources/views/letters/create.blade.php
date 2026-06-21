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
                <div class="flex justify-end mt-12">
                    <div class="text-center w-64">
                        <div>Panjalu, <span x-text="formattedDate"></span></div>
                        <div>Mengetahui,</div>
                        <div>Kepala Sekolah</div>
                        <div class="font-bold">SMA AL MANSHUR</div>
                        
                        <div class="h-20"></div> <!-- Space for signature -->
                        
                        <div class="font-bold underline">Tini Sonjaya,S.Pd.,Gr</div>
                    </div>
                </div>

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
