<?php

namespace App\Http\Controllers;

use App\Models\Letter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LetterController extends Controller
{
    private function getLetterTypes()
    {
        return \App\Models\Template::pluck('name', 'code')->toArray();
    }

    public function create()
    {
        $letterTypes = $this->getLetterTypes();

        $sequences = [];
        foreach ($letterTypes as $code => $name) {
            $count = Letter::where('type_code', $code)->count();
            $sequences[$code] = str_pad($count + 1, 2, '0', STR_PAD_LEFT);
        }

        $pejabats = \App\Models\Pejabat::orderBy('is_active', 'desc')->get();

        return view('letters.create', compact('letterTypes', 'sequences', 'pejabats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type_code' => 'required|string',
            'letter_number' => 'required|string',
            'event_name' => 'nullable|string',
            'letter_date' => 'required|date',
            'destination' => 'nullable|string',
            'content' => 'required|string',
            'jumlah_ttd' => 'required|in:1,2',
            'kepsek_id' => 'required|exists:pejabats,id',
            'pihak1_name' => 'nullable|required_if:jumlah_ttd,2|string|max:255',
        ]);

        $letter = new Letter($validated);
        $letter->user_id = Auth::id();
        
        $types = $this->getLetterTypes();
        
        $typeName = $types[$request->type_code] ?? 'Surat';
        $eventName = $request->event_name ? $request->event_name : 'Kegiatan';
        $letter->title = $typeName . ' - ' . $eventName;
        
        // Tentukan status berdasarkan tombol yang diklik
        if ($request->input('action') === 'draft') {
            $letter->status = 'draft';
        } else {
            $letter->status = $request->jumlah_ttd == 2 ? 'menunggu_persetujuan_pihak1' : 'pending';
        }
        $letter->save();

        $message = $letter->status === 'draft' 
            ? 'Surat berhasil disimpan sebagai draft.' 
            : 'Surat berhasil diajukan dan menunggu persetujuan Kepala Sekolah.';

        return redirect()->route('dashboard')->with('success', $message);
    }

    public function index()
    {
        $letterTypes = $this->getLetterTypes();

        $query = Letter::with('user')->latest();

        // Filter pencarian
        if (request('search')) {
            $query->where(function ($q) {
                $q->where('letter_number', 'like', '%' . request('search') . '%')
                  ->orWhere('event_name', 'like', '%' . request('search') . '%')
                  ->orWhere('title', 'like', '%' . request('search') . '%');
            });
        }

        // Filter jenis surat
        if (request('jenis') && request('jenis') !== 'semua') {
            $query->where('type_code', request('jenis'));
        }

        // Filter status
        if (request('status') && request('status') !== 'semua') {
            $query->where('status', request('status'));
        }

        $letters = $query->paginate(10)->withQueryString();

        return view('letters.index', compact('letters', 'letterTypes'));
    }

    public function show(Letter $letter)
    {
        $letterTypes = $this->getLetterTypes();
        return view('letters.show', compact('letter', 'letterTypes'));
    }

    public function edit(Letter $letter)
    {
        $letterTypes = $this->getLetterTypes();
        $sequences = [];
        foreach ($letterTypes as $code => $name) {
            $count = Letter::where('type_code', $code)->where('id', '!=', $letter->id)->count();
            $sequences[$code] = str_pad($count + 1, 2, '0', STR_PAD_LEFT);
        }
        $pejabats = \App\Models\Pejabat::orderBy('is_active', 'desc')->get();
        return view('letters.edit', compact('letter', 'letterTypes', 'sequences', 'pejabats'));
    }

    public function update(Request $request, Letter $letter)
    {
        $validated = $request->validate([
            'type_code'   => 'required|string',
            'letter_number' => 'required|string',
            'event_name'  => 'nullable|string',
            'letter_date' => 'required|date',
            'destination' => 'nullable|string',
            'content'     => 'required|string',
            'jumlah_ttd'  => 'required|in:1,2',
            'kepsek_id'   => 'required|exists:pejabats,id',
            'pihak1_name'   => 'nullable|required_if:jumlah_ttd,2|string|max:255',
        ]);

        $types = $this->getLetterTypes();

        $letter->fill($validated);
        $letter->title = ($types[$request->type_code] ?? 'Surat') . ' - ' . ($request->event_name ?: 'Kegiatan');
        if ($request->input('action') === 'draft') {
            $letter->status = 'draft';
        } else {
            $letter->status = $request->jumlah_ttd == 2 ? 'menunggu_persetujuan_pihak1' : 'pending';
        }
        $letter->save();

        $message = $letter->status === 'draft'
            ? 'Surat berhasil diperbarui sebagai draft.'
            : 'Surat berhasil diperbarui dan diajukan kembali.';

        return redirect()->route('letters.index')->with('success', $message);
    }

    public function destroy(Letter $letter)
    {
        $letter->delete();
        return redirect()->route('letters.index')->with('success', 'Surat berhasil dihapus.');
    }

    public function arsip()
    {
        $letterTypes = $this->getLetterTypes();

        $query = Letter::with(['user', 'reviewer'])
            ->whereIn('status', ['approved', 'rejected'])
            ->latest('reviewed_at');

        if (request('search')) {
            $query->where(function ($q) {
                $q->where('letter_number', 'like', '%' . request('search') . '%')
                  ->orWhere('event_name', 'like', '%' . request('search') . '%')
                  ->orWhere('title', 'like', '%' . request('search') . '%');
            });
        }

        if (request('jenis') && request('jenis') !== 'semua') {
            $query->where('type_code', request('jenis'));
        }

        $letters = $query->paginate(10)->withQueryString();

        return view('letters.arsip', compact('letters', 'letterTypes'));
    }

    public function showArsip(Letter $letter)
    {
        $letterTypes = $this->getLetterTypes();

        return view('letters.arsip-show', compact('letter', 'letterTypes'));
    }

    public function storePejabat(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nip' => 'nullable|string|max:255',
        ]);

        // Nonaktifkan semua pejabat lain
        \App\Models\Pejabat::query()->update(['is_active' => false]);

        $pejabat = \App\Models\Pejabat::create([
            'nama' => $validated['nama'],
            'nip' => $validated['nip'] ?? null,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'pejabat' => $pejabat
        ]);
    }
}
