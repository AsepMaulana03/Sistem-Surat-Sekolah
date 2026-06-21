@extends('layouts.admin')

@section('page_title', 'Template Surat')

@section('content')
<div x-data="templateManager({{ $templates->toJson() }})" class="flex flex-col h-full">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Template Surat</h1>
            <p class="text-xs text-gray-400 mt-0.5">{{ $templates->count() }} template tersedia</p>
        </div>
        <button @click="showModal = true"
            class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-900 hover:bg-gray-700 text-white text-sm font-semibold rounded-xl transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Template
        </button>
    </div>

    <div class="flex flex-col lg:flex-row gap-6 items-start">

        <!-- Kolom Kiri: Daftar Template -->
        <div class="w-full lg:w-72 flex-shrink-0 flex flex-col gap-3">

            <!-- Search -->
            <div class="relative">
                <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" x-model="searchQuery" placeholder="Cari template..."
                    class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition">
            </div>

            <!-- Template List -->
            <div class="flex flex-col gap-1.5 max-h-[680px] overflow-y-auto pr-1">
                <template x-for="template in filteredTemplates" :key="template.id">
                    <div @click="selectTemplate(template)"
                         :class="selectedTemplate && selectedTemplate.id === template.id
                             ? 'bg-gray-900 text-white shadow-sm'
                             : 'bg-white border border-gray-100 text-gray-700 hover:bg-gray-50'"
                         class="p-4 rounded-xl cursor-pointer transition-all duration-150 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg flex-shrink-0 flex items-center justify-center"
                             :class="selectedTemplate && selectedTemplate.id === template.id ? 'bg-white/10' : 'bg-gray-100'">
                            <svg class="w-4 h-4" :class="selectedTemplate && selectedTemplate.id === template.id ? 'text-white' : 'text-gray-500'"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold truncate" x-text="template.name"></p>
                            <p class="text-xs opacity-60 mt-0.5" x-text="'Kode: ' + template.code"></p>
                        </div>
                    </div>
                </template>
                <div x-show="filteredTemplates.length === 0" class="text-center py-10 text-gray-400 text-sm">
                    Tidak ada template ditemukan.
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Preview -->
        <div class="flex-1 min-w-0">

            <!-- Nama Template Input -->
            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Nama Template Dipilih</label>
                <input type="text" :value="selectedTemplate ? selectedTemplate.name : ''" readonly
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm bg-white outline-none text-gray-700 font-semibold">
            </div>

            <!-- Preview Surat A4 -->
            <div x-show="selectedTemplate" style="display: none;" class="overflow-x-auto">
                <div class="bg-white p-10 md:p-14 shadow-sm border border-gray-100 rounded-2xl text-[13px] text-black mx-auto"
                     style="font-family: 'Times New Roman', Times, serif; min-height: 297mm; max-width: 210mm;">

                    <!-- KOP Surat -->
                    <div class="flex items-center justify-between border-b-[3px] border-black pb-4 mb-6">
                        <img src="{{ asset('images/logo_yayasan.png') }}" alt="Logo Yayasan" class="w-24 h-24 object-contain flex-shrink-0">
                        <div class="text-center px-4 flex-1">
                            <div class="font-bold text-[13px] tracking-wide uppercase">YAYASAN JAUHARUL HUDA AL-ALMANSHUIR</div>
                            <div class="font-bold text-[15px] tracking-wider uppercase">SEKOLAH MENENGAH ATAS</div>
                            <div class="font-bold text-[18px] tracking-widest uppercase">AL MANSHUR</div>
                            <div class="text-[11px] font-semibold">Terakreditasi "A"</div>
                            <div class="text-[10px] mt-1">Alamat: Jl. Kawali-Panjalu KM 07 Desa Sandingtaman Kec Panjalu Kab Ciamis Jawa Barat 46264</div>
                            <div class="text-[10px]">Tlp: 082217803253 email: <span>almanshurpublisher01@gmail.com</span></div>
                        </div>
                        <img src="{{ asset('images/logo_sekolah.png') }}" alt="Logo Sekolah" class="w-24 h-24 object-contain flex-shrink-0">
                    </div>

                    <!-- Nomor & Lampiran -->
                    <div class="mb-6">
                        <table>
                            <tr><td class="w-20 align-top">Nomor</td><td class="w-4 align-top text-center">:</td><td>(nomor surat isi otomatis)</td></tr>
                            <tr><td class="align-top">Lampiran</td><td class="align-top text-center">:</td><td>-</td></tr>
                            <tr><td class="align-top">Perihal</td><td class="align-top text-center">:</td><td>(nama kegiatan isi otomatis)</td></tr>
                        </table>
                    </div>

                    <!-- Tujuan -->
                    <div class="mb-6">
                        <div>Kepada Yth,</div>
                        <div>(Tujuan isi otomatis)</div>
                        <div>di tempat</div>
                    </div>

                    <div class="text-center italic mb-4 mt-8">Assalamu'alaikum warahmatullahi wabarakatuh</div>

                    <div class="text-justify leading-relaxed whitespace-pre-wrap min-h-[100px]" x-text="selectedTemplate ? selectedTemplate.content : ''"></div>

                    <div class="text-center italic mt-8 mb-8">Wassalamu'alaikum warahmatullahi wabarakatuh</div>

                    <div class="flex justify-end mt-12">
                        <div class="text-center w-64">
                            <div>Panjalu, (tanggal isi otomatis)</div>
                            <div>Mengetahui,</div>
                            <div>Kepala Sekolah</div>
                            <div class="font-bold">SMA AL MANSHUR</div>
                            <div class="h-20"></div>
                            <div class="font-bold underline">Tini Sonjaya,S.Pd.,Gr</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div x-show="!selectedTemplate"
                class="h-[500px] bg-white rounded-2xl border border-dashed border-gray-200 flex flex-col items-center justify-center text-center">
                <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-400">Pilih template dari daftar</p>
                <p class="text-xs text-gray-300 mt-1">Preview akan tampil di sini</p>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Template -->
    <div x-show="showModal" style="display: none;"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm" x-transition.opacity>
        <div class="bg-white rounded-2xl shadow-xl p-8 max-w-md w-full mx-4 relative" @click.away="showModal = false">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-900">Tambah Template Baru</h3>
                <button type="button" @click="showModal = false" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('templates.store') }}" class="flex flex-col gap-5">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Nama Template</label>
                    <input type="text" name="name" required placeholder="Contoh: Surat Edaran"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition">
                </div>
                <div class="flex gap-3 pt-2 border-t border-gray-50">
                    <button type="button" @click="showModal = false"
                        class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-600 font-semibold rounded-xl hover:bg-gray-50 transition text-sm">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-2.5 bg-gray-900 hover:bg-gray-700 text-white font-semibold rounded-xl transition text-sm">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('templateManager', (initialTemplates) => ({
            templates: initialTemplates,
            searchQuery: '',
            selectedTemplate: initialTemplates.length > 0 ? initialTemplates[0] : null,
            showModal: false,
            get filteredTemplates() {
                if (this.searchQuery === '') return this.templates;
                const q = this.searchQuery.toLowerCase();
                return this.templates.filter(t => t.name.toLowerCase().includes(q));
            },
            selectTemplate(template) {
                this.selectedTemplate = template;
            }
        }))
    });
</script>
@endsection
