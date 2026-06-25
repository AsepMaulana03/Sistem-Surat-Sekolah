@extends('layouts.admin')

@section('page_title', 'Detail Arsip')

@section('content')
<style>
    @media print {
        body * { visibility: hidden; }
        #printableArea, #printableArea * { visibility: visible; }
        #printableArea { position: absolute; left: 0; top: 0; width: 100%; margin: 0; padding: 0; box-shadow: none !important; border: none !important; }
    }
</style>

<div>
    <!-- Header -->
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('letters.arsip') }}" class="p-2 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-xl transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">Detail Surat Arsip</h1>
            <p class="text-xs text-gray-400 mt-0.5">{{ $letter->letter_number ?? 'Tanpa Nomor' }}</p>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-6 items-start">

        <!-- Panel Kiri: Info & Aksi -->
        <div class="w-full lg:w-64 flex-shrink-0 flex flex-col gap-4">

            <!-- Info Card -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex flex-col gap-4">
                <div>
                    <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Nomor Surat</p>
                    <p class="text-sm font-semibold text-gray-900">{{ $letter->letter_number ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Jenis Surat</p>
                    <p class="text-sm font-medium text-gray-700">{{ $letterTypes[$letter->type_code] ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Tanggal</p>
                    <p class="text-sm font-medium text-gray-700">{{ $letter->letter_date ? \Carbon\Carbon::parse($letter->letter_date)->isoFormat('D MMMM Y') : '-' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Diajukan Oleh</p>
                    <p class="text-sm font-medium text-gray-700">{{ $letter->user->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Status</p>
                    @if($letter->status === 'approved')
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-green-50 text-green-600">✓ Disetujui</span>
                    @elseif($letter->status === 'rejected')
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-red-50 text-red-500">✕ Ditolak</span>
                    @endif
                </div>
                @if($letter->status === 'rejected' && $letter->rejection_note)
                <div class="p-3 bg-red-50 rounded-xl border border-red-100">
                    <p class="text-[10px] font-semibold text-red-400 uppercase tracking-wider mb-1">Catatan Penolakan</p>
                    <p class="text-xs text-red-600">{{ $letter->rejection_note }}</p>
                </div>
                @endif
            </div>

            <!-- Action Buttons -->
            <button onclick="downloadPDF()"
                class="w-full py-3 px-4 bg-gray-900 hover:bg-gray-700 text-white text-sm font-semibold rounded-xl transition shadow-sm flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Download PDF
            </button>
            <button onclick="window.print()"
                class="w-full py-3 px-4 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-semibold rounded-xl transition shadow-sm flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Print
            </button>
        </div>

        <!-- Preview A4 -->
        <div class="flex-1 overflow-x-auto">
            <div id="printableArea" class="bg-white p-10 md:p-14 shadow-sm border border-gray-100 rounded-2xl text-[13px] text-black mx-auto" style="font-family: 'Times New Roman', Times, serif; min-height: 297mm; max-width: 210mm;">
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

                <!-- Nomor -->
                <div class="mb-6">
                    <table>
                        <tr><td class="w-20 align-top">Nomor</td><td class="w-4 align-top text-center">:</td><td>{{ $letter->letter_number ?? '-' }}</td></tr>
                        <tr><td class="align-top">Lampiran</td><td class="align-top text-center">:</td><td>-</td></tr>
                        <tr><td class="align-top">Perihal</td><td class="align-top text-center">:</td><td>{{ $letter->event_name ?? '-' }}</td></tr>
                    </table>
                </div>

                <!-- Tujuan -->
                <div class="mb-6">
                    <div>Kepada Yth,</div>
                    <div>{{ $letter->destination ?? '-' }}</div>
                    <div>di tempat</div>
                </div>

                <div class="text-center italic mb-4 mt-8">Assalamu'alaikum warahmatullahi wabarakatuh</div>

                <div class="text-justify leading-relaxed whitespace-pre-wrap">{{ $letter->content }}</div>

                <div class="text-center italic mt-8 mb-8">Wassalamu'alaikum warahmatullahi wabarakatuh</div>

                <!-- Tanda Tangan -->
                <div class="mt-12 flex {{ $letter->jumlah_ttd == 2 ? 'justify-between' : 'justify-end' }}">
                    @if($letter->jumlah_ttd == 2)
                    <div class="text-center w-64">
                        <div class="h-6"></div> <!-- Spacer -->
                        <div>Mengetahui,</div>
                        <div>Pihak 1 / Guru Pendamping</div>
                        <div class="font-bold">SMA AL MANSHUR</div>
                        <div class="h-20"></div>
                        <div class="font-bold underline">{{ $letter->pihak1_name ?? '-' }}</div>
                    </div>
                    @endif

                    <div class="text-center w-64">
                        <div>Panjalu, {{ $letter->letter_date ? \Carbon\Carbon::parse($letter->letter_date)->isoFormat('D MMMM Y') : '-' }}</div>
                        <div>Mengetahui,</div>
                        <div>Kepala Sekolah</div>
                        <div class="font-bold">SMA AL MANSHUR</div>
                        <div class="h-20"></div>
                        @php
                            $kepsekName = \App\Models\Pejabat::find($letter->kepsek_id)?->nama ?? 'Tini Sonjaya,S.Pd.,Gr';
                        @endphp
                        <div class="font-bold underline">{{ $kepsekName }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    function downloadPDF() {
        var element = document.getElementById('printableArea');
        var opt = {
            margin: [0, 0, 0, 0],
            filename: '{{ $letter->letter_number ? str_replace("/", "_", $letter->letter_number) : "Surat_Arsip" }}.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, useCORS: true },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };
        html2pdf().set(opt).from(element).save();
    }
</script>
@endsection
