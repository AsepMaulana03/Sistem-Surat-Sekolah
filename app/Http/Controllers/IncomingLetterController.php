<?php

namespace App\Http\Controllers;

use App\Models\IncomingLetter;
use Illuminate\Http\Request;
use thiagoalessio\TesseractOCR\TesseractOCR;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class IncomingLetterController extends Controller
{
    public function index()
    {
        // For Staf TU: show all incoming letters. For Kepsek: show all as well.
        $letters = IncomingLetter::with('creator')->latest()->get();
        return view('incoming_letters.index', compact('letters'));
    }

    public function create()
    {
        // Step 1: Upload file
        return view('incoming_letters.upload');
    }

    public function ocr(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        // Save the file temporarily or permanently
        $path = $request->file('file')->store('incoming_letters', 'public');

        // REAL OCR ENGINE: Extracting text using Tesseract
        try {
            $fullPath = storage_path('app/public/' . $path);
            
            $tesseract = new TesseractOCR($fullPath);
            // On Windows, if tesseract is not in PATH, we must specify the executable path
            $tesseract->executable('C:\Program Files\Tesseract-OCR\tesseract.exe');
            
            $text = $tesseract->run();
            
            // Basic parsing logic to find No Surat, Perihal, Asal Surat
            $noSurat = '';
            $perihal = '';
            $asalSurat = '';
            
            $lines = explode("\n", $text);
            foreach ($lines as $line) {
                $line = trim($line);
                if (preg_match('/(?:Nomor|No)[\s\.:]+(.+)/i', $line, $matches)) {
                    $noSurat = trim($matches[1]);
                }
                if (preg_match('/(?:Perihal|Hal)[\s\.:]+(.+)/i', $line, $matches)) {
                    $perihal = trim($matches[1]);
                }
            }
            
            // Asal surat might be at the top of the letter. Grab the first non-empty string.
            foreach ($lines as $line) {
                $line = trim($line);
                if (!empty($line) && strlen($line) > 5) {
                    $asalSurat = $line;
                    break;
                }
            }

            $ocrData = [
                'no_surat' => $noSurat ?: ('SM.' . date('Ymd') . '.' . rand(100, 999)),
                'perihal' => $perihal ?: 'Hasil Scan Tidak Jelas (Silakan Edit)',
                'asal_surat' => $asalSurat ?: 'Hasil Scan Tidak Jelas (Silakan Edit)',
            ];
            
        } catch (\Exception $e) {
            // Fallback if OCR fails completely
            $ocrData = [
                'no_surat' => 'ERROR_OCR',
                'perihal' => 'Gagal membaca dokumen: ' . substr($e->getMessage(), 0, 100),
                'asal_surat' => 'Silakan ketik manual',
            ];
        }

        return view('incoming_letters.form', [
            'file_path' => $path,
            'ocr_data' => $ocrData,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'file_path' => 'required|string',
            'no_surat' => 'required|string|max:255',
            'perihal' => 'required|string|max:255',
            'asal_surat' => 'required|string|max:255',
        ]);

        IncomingLetter::create([
            'file_path' => $request->file_path,
            'no_surat' => $request->no_surat,
            'perihal' => $request->perihal,
            'asal_surat' => $request->asal_surat,
            'status' => 'menunggu_disposisi',
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('incoming-letters.index')->with('success', 'Surat Masuk berhasil disimpan dan menunggu disposisi.');
    }

    public function show(IncomingLetter $incomingLetter)
    {
        return view('incoming_letters.show', compact('incomingLetter'));
    }

    public function disposisi(Request $request, IncomingLetter $incomingLetter)
    {
        // Only Kepsek should do this
        $roleName = optional(Auth::user()->role)->name ?? '';
        if (str_contains(strtolower($roleName), 'kepala') || str_contains(strtolower(Auth::user()->name), 'kepala') || $roleName === 'admin' || Auth::user()->role_id == 2) {
            $request->validate([
                'instruksi_disposisi' => 'required|string',
            ]);

            $incomingLetter->update([
                'instruksi_disposisi' => $request->instruksi_disposisi,
                'status' => 'selesai',
                'disposisi_at' => now(),
            ]);

            return redirect()->route('incoming-letters.index')->with('success', 'Disposisi berhasil disimpan dan surat telah terarsip.');
        }

        return back()->with('error', 'Anda tidak memiliki akses untuk melakukan disposisi.');
    }
}
