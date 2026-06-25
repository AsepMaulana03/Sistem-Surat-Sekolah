<?php

namespace App\Http\Controllers;

use App\Models\Letter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Pihak1Controller extends Controller
{
    private function getLetterTypes()
    {
        return \App\Models\Template::pluck('name', 'code')->toArray();
    }

    /**
     * Halaman Approval: tampilkan surat berstatus menunggu_persetujuan_pihak1
     */
    public function approval(Request $request)
    {
        $query = Letter::with('user')
            ->where('status', 'menunggu_persetujuan_pihak1')
            ->where('pihak1_id', Auth::id())
            ->latest();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('letter_number', 'like', "%{$request->search}%")
                  ->orWhere('event_name', 'like', "%{$request->search}%");
            });
        }

        if ($request->jenis && $request->jenis !== 'semua') {
            $query->where('type_code', $request->jenis);
        }

        $letters = $query->paginate(10)->withQueryString();
        $letterTypes = $this->getLetterTypes();

        return view('guru.approval', compact('letters', 'letterTypes'));
    }

    /**
     * Setujui Surat (teruskan ke Kepsek)
     */
    public function approve(Letter $letter)
    {
        // Pastikan hanya surat yang menjadi kewenangan Pihak 1 ini
        if ($letter->pihak1_id !== Auth::id()) {
            return back()->with('error', 'Anda tidak berhak menyetujui surat ini.');
        }

        $letter->update([
            'status' => 'pending', // Lanjut ke Kepsek
        ]);

        return back()->with('success', "Surat \"{$letter->letter_number}\" berhasil disetujui dan diteruskan ke Kepala Sekolah.");
    }

    /**
     * Tolak Surat
     */
    public function reject(Request $request, Letter $letter)
    {
        if ($letter->pihak1_id !== Auth::id()) {
            return back()->with('error', 'Anda tidak berhak menolak surat ini.');
        }

        $request->validate([
            'rejection_note' => 'nullable|string|max:500',
        ]);

        $letter->update([
            'status'         => 'rejected',
            'rejection_note' => $request->rejection_note,
        ]);

        return back()->with('success', "Surat \"{$letter->letter_number}\" telah ditolak.");
    }

    /**
     * Halaman Arsip: tampilkan surat yang sudah disetujui (diteruskan/approved/rejected)
     */
    public function arsip(Request $request)
    {
        // Surat yang terkait dengan pihak1_id ini, yang statusnya sudah bukan menunggu_persetujuan_pihak1
        $query = Letter::with(['user', 'reviewer'])
            ->where('pihak1_id', Auth::id())
            ->where('status', '!=', 'menunggu_persetujuan_pihak1')
            ->where('status', '!=', 'draft')
            ->latest('updated_at');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('letter_number', 'like', "%{$request->search}%")
                  ->orWhere('event_name', 'like', "%{$request->search}%");
            });
        }

        if ($request->jenis && $request->jenis !== 'semua') {
            $query->where('type_code', $request->jenis);
        }

        if ($request->status && $request->status !== 'semua') {
            $query->where('status', $request->status);
        }

        $letters = $query->paginate(10)->withQueryString();
        $letterTypes = $this->getLetterTypes();

        return view('guru.arsip', compact('letters', 'letterTypes'));
    }

    /**
     * Preview detail surat untuk Pihak 1
     */
    public function show(Letter $letter)
    {
        if ($letter->pihak1_id !== Auth::id()) {
            return back()->with('error', 'Anda tidak memiliki akses ke surat ini.');
        }

        $letterTypes = $this->getLetterTypes();
        return view('guru.show', compact('letter', 'letterTypes'));
    }
}
