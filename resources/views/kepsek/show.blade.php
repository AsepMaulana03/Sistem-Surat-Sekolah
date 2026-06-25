@extends('layouts.kepsek')

@section('content')
<div>
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Detail Surat</h1>
            <p class="text-sm text-gray-500 mt-0.5">Preview lengkap dokumen surat</p>
        </div>
        <div class="flex items-center gap-3">
            @php
                $statusConfig = [
                    'draft'=>['bg-gray-100 text-gray-600','Draft'],
                    'menunggu_persetujuan_pihak1' => ['bg-blue-50 text-blue-600', 'Menunggu Pihak 1'],
                    'pending'=>['bg-amber-50 text-amber-600','Menunggu Kepsek'],
                    'approved'=>['bg-green-50 text-green-600','Disetujui'],
                    'rejected'=>['bg-red-50 text-red-500','Ditolak']
                ];
                [$cls,$lbl] = $statusConfig[$letter->status] ?? ['bg-gray-100 text-gray-600', ucfirst($letter->status)];
            @endphp
            <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold {{ $cls }}">{{ $lbl }}</span>
            <a href="{{ url()->previous() }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
        </div>
    </div>

    <!-- Catatan penolakan (jika ada) -->
    @if($letter->status === 'rejected' && $letter->rejection_note)
    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-400 rounded-r-lg">
        <p class="text-sm font-semibold text-red-700 mb-1">Catatan Penolakan:</p>
        <p class="text-sm text-red-600">{{ $letter->rejection_note }}</p>
    </div>
    @endif

    <!-- Preview A4 -->
    <div class="bg-white p-12 shadow-md border border-gray-200 rounded-lg mx-auto text-[13px] text-black max-w-3xl" style="font-family: 'Times New Roman', Times, serif;">
        <!-- KOP Surat -->
        <div class="flex items-center justify-between border-b-4 border-black pb-4 mb-6">
            <img src="{{ asset('images/logo_yayasan.png') }}" alt="Logo Yayasan" class="w-24 h-24 object-contain flex-shrink-0">
            <div class="text-center px-4">
                <div class="font-bold text-sm tracking-wide">YAYASAN JAUHARUL HUDA AL-ALMANSHUIR</div>
                <div class="font-bold text-lg tracking-wider">SEKOLAH MENENGAH ATAS</div>
                <div class="font-bold text-xl tracking-widest">AL MANSHUR</div>
                <div class="text-[11px] font-semibold">Terakreditasi "A"</div>
                <div class="text-[10px]">Alamat: Jl. Kawali-Panjalu KM 07 Desa Sandingtaman Kec Panjalu Kab Ciamis Jawa Barat 46264</div>
                <div class="text-[10px]">Tlp: 082217803253 email: <span class="text-blue-600">almanshurpublisher01@gmail.com</span></div>
            </div>
            <img src="{{ asset('images/logo_sekolah.png') }}" alt="Logo Sekolah" class="w-24 h-24 object-contain flex-shrink-0">
        </div>

        <!-- Nomor -->
        <div class="mb-6">
            <table>
                <tr><td class="w-24 align-top">Nomor</td><td class="w-4 align-top">:</td><td>{{ $letter->letter_number ?? '-' }}</td></tr>
                <tr><td class="align-top">Lampiran</td><td class="align-top">:</td><td>-</td></tr>
                <tr><td class="align-top">Perihal</td><td class="align-top">:</td><td>{{ $letter->event_name ?? '-' }}</td></tr>
            </table>
        </div>

        <!-- Tujuan -->
        <div class="mb-6">
            <div>Kepada Yth,</div>
            <div>{{ $letter->destination ?? '-' }}</div>
            <div>di tempat</div>
        </div>

        <div class="text-center italic mb-4">Assalamu'alaikum warahmatullahi wabarakatuh</div>

        <div class="text-justify leading-relaxed whitespace-pre-wrap">{{ $letter->content }}</div>

        <div class="text-center italic mt-6 mb-8">Wassalamu'alaikum warahmatullahi wabarakatuh</div>

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
@endsection
