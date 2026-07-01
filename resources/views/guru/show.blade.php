@extends('layouts.admin')

@section('content')
<div>
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Detail Surat (Pihak 1)</h1>
            <p class="text-sm text-gray-500 mt-0.5">Preview lengkap dokumen surat</p>
        </div>
        <div class="flex items-center gap-3">
            @php
                $statusConfig = ['draft'=>['bg-gray-100 text-gray-600','Draft'],'pending'=>['bg-amber-50 text-amber-600','Pending Kepsek'],'approved'=>['bg-green-50 text-green-600','Disetujui Kepsek'],'rejected'=>['bg-red-50 text-red-500','Ditolak'],'menunggu_persetujuan_pihak1'=>['bg-blue-50 text-blue-600','Menunggu Persetujuan Anda']];
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
        <div class="flex items-center justify-between border-b-[5px] border-double border-gray-600 pb-2 mb-4 relative">
            <div class="absolute bottom-0 left-0 w-full border-b-[2px] border-black" style="margin-bottom: -4px;"></div>
            <img src="{{ asset('images/logo_yayasan.png') }}" alt="Logo Yayasan" class="w-24 h-24 object-contain flex-shrink-0 z-10 relative">
            <div class="text-center px-4 z-10 relative w-full">
                <div class="font-bold text-sm tracking-wide uppercase">YAYASAN JAUHARUL HUDA AL-MANSHUR</div>
                <div class="font-bold text-lg tracking-wider">SEKOLAH MENENGAH ATAS</div>
                <div class="font-bold text-xl tracking-widest">AL MANSHUR</div>
                <div class="text-[12px] font-bold uppercase mt-0.5">TERAKREDITASI "A"</div>
                <div class="text-[10px] font-semibold">Nomor: 163/BAN-PDM/SK/2025</div>
                <div class="text-[10px] mt-0.5">Alamat: Jl. Kawali-Panjalu KM 07 Desa Sandingtaman Kec.Panjalu Kab. Ciamis Jawa Barat 46264</div>
                <div class="text-[10px]">Tlp: 082217803253 email: <span class="text-blue-600 underline">almanshurpublisher01@gmail.com</span></div>
            </div>
            <img src="{{ asset('images/logo_sekolah.png') }}" alt="Logo Sekolah" class="w-24 h-24 object-contain flex-shrink-0 z-10 relative">
        </div>

        <!-- Nomor -->
        <div class="mb-4">
            <table>
                <tr><td class="w-24 align-top">Nomor</td><td class="w-4 align-top">:</td><td>{{ $letter->letter_number ?? '-' }}</td></tr>
                <tr><td class="align-top">Lampiran</td><td class="align-top">:</td><td>-</td></tr>
                <tr><td class="align-top">Perihal</td><td class="align-top">:</td><td>{{ $letter->event_name ?? '-' }}</td></tr>
            </table>
        </div>

        <!-- Tujuan -->
        <div class="mb-4 mt-2">
            <div>Kepada:</div>
            <div class="whitespace-pre-line">Yth. {{ str_replace('Yth. ', '', $letter->destination ?? '-') }}</div>
            <div>di Tempat</div>
        </div>

        <div class="text-center italic mb-4 mt-4">Assalamu'alaikum warahmatullahi wabarakatuh,</div>

        <div class="leading-relaxed mt-4">
            @php
                $lines = explode("\n", $letter->content);
            @endphp
            @foreach($lines as $line)
                @php
                    $trimmed = trim($line);
                    $colonPos = strpos($trimmed, ':');
                @endphp
                @if($trimmed === '')
                    <div class="h-2"></div>
                @elseif($colonPos !== false && $colonPos > 0 && $colonPos < 35)
                    <div class="flex ml-10 my-1">
                        <div class="w-[140px]">{{ trim(substr($trimmed, 0, $colonPos)) }}</div>
                        <div class="w-4">:</div>
                        <div class="flex-1">{{ ltrim(substr($trimmed, $colonPos + 1)) }}</div>
                    </div>
                @else
                    <div class="text-justify mb-1">
                        {{ $trimmed }}
                    </div>
                @endif
            @endforeach
        </div>

        <div class="text-center italic mt-5 mb-5">Wassalamu'alaikum warahmatullahi wabarakatuh.</div>

        <div class="mt-6 flex {{ $letter->jumlah_ttd == 2 ? 'justify-between' : 'justify-end' }}">
            @if($letter->jumlah_ttd == 2)
            <div class="text-center w-64">
                <div>&nbsp;</div>
                <div>&nbsp;</div>
                <div>Guru Pendamping</div>
                <div class="h-24 relative"></div>
                <div class="font-bold">{{ $letter->pihak1_name ?? '-' }}</div>
            </div>
            @endif

            <div class="text-center w-64">
                <div>Panjalu, {{ $letter->letter_date ? \Carbon\Carbon::parse($letter->letter_date)->isoFormat('D MMMM Y') : '-' }}</div>
                <div>Mengetahui,</div>
                <div>Kepala Sekolah</div>
                <div class="font-bold">SMA AL MANSHUR</div>
                <div class="h-24 relative"></div>
                @php
                    $kepsekName = \App\Models\Pejabat::find($letter->kepsek_id)?->nama ?? 'Tini Sonjaya, S.Pd., Gr';
                @endphp
                <div class="font-bold">{{ $kepsekName }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
