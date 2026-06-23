<?php

namespace App\Http\Controllers;

use App\Models\Letter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KepsekController extends Controller
{
    private function getLetterTypes()
    {
        return \App\Models\Template::pluck('name', 'code')->toArray();
    }

    /**
     * Halaman Approval: tampilkan surat berstatus pending
     */
    public function approval(Request $request)
    {
        $query = Letter::with('user')
            ->where('status', 'pending')
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
        
        $incomingPending = \App\Models\IncomingLetter::where('status', 'menunggu_disposisi')->count();

        return view('kepsek.approval', compact('letters', 'letterTypes', 'incomingPending'));
    }

    /**
     * Setujui Surat
     */
    public function approve(Letter $letter)
    {
        $letter->update([
            'status'      => 'approved',
            'reviewed_at' => now(),
            'reviewed_by' => Auth::id(),
            'rejection_note' => null,
        ]);

        return back()->with('success', "Surat \"{$letter->letter_number}\" berhasil disetujui.");
    }

    /**
     * Tolak Surat
     */
    public function reject(Request $request, Letter $letter)
    {
        $request->validate([
            'rejection_note' => 'nullable|string|max:500',
        ]);

        $letter->update([
            'status'         => 'rejected',
            'reviewed_at'    => now(),
            'reviewed_by'    => Auth::id(),
            'rejection_note' => $request->rejection_note,
        ]);

        return back()->with('success', "Surat \"{$letter->letter_number}\" telah ditolak.");
    }

    /**
     * Halaman Arsip: tampilkan surat yang sudah disetujui atau ditolak
     */
    public function arsip(Request $request)
    {
        $query = Letter::with(['user', 'reviewer'])
            ->whereIn('status', ['approved', 'rejected'])
            ->latest('reviewed_at');

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

        return view('kepsek.arsip', compact('letters', 'letterTypes'));
    }

    /**
     * Preview detail surat untuk kepsek
     */
    public function show(Letter $letter)
    {
        $letterTypes = $this->getLetterTypes();
        return view('kepsek.show', compact('letter', 'letterTypes'));
    }
}
