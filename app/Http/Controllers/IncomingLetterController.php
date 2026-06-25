<?php

namespace App\Http\Controllers;

use App\Models\IncomingLetter;
use Illuminate\Http\Request;
use thiagoalessio\TesseractOCR\TesseractOCR;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class IncomingLetterController extends Controller
{
    public function index(Request $request)
    {
        // For Staf TU: show all incoming letters. For Kepsek: show all as well.
        $query = IncomingLetter::with('creator');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('no_surat', 'like', "%{$search}%")
                  ->orWhere('perihal', 'like', "%{$search}%")
                  ->orWhere('asal_surat', 'like', "%{$search}%")
                  ->orWhere('tujuan', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('status', $request->status);
        }

        $letters = $query->latest()->get();
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

        // REAL OCR ENGINE / PDF Parser: Extracting text
        try {
            $fullPath = storage_path('app/public/' . $path);
            $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
            $text = '';
            
            if ($ext === 'pdf') {
                $parser = new \Smalot\PdfParser\Parser();
                $pdf = $parser->parseFile($fullPath);
                $text = trim($pdf->getText());

                // Jika hasil parse kosong/sedikit, berarti ini PDF hasil scan. Gunakan Ghostscript + Tesseract
                if (strlen($text) < 50) {
                    $gsPath = 'C:\Program Files\gs\gs10.07.1\bin\gswin64c.exe';
                    if (!file_exists($gsPath)) {
                        $gsPath = 'gswin64c'; // Fallback if added to PATH
                    }
                    
                    $tempImage = storage_path('app/public/temp_ocr_' . uniqid() . '.png');
                    $cmd = sprintf('"%s" -dQUIET -dPARANOIDSAFER -dBATCH -dNOPAUSE -dNOPROMPT -sDEVICE=png16m -dTextAlphaBits=4 -dGraphicsAlphaBits=4 -r300 -dFirstPage=1 -dLastPage=1 -sOutputFile="%s" "%s"', $gsPath, $tempImage, $fullPath);
                    shell_exec($cmd);
                    
                    if (file_exists($tempImage)) {
                        $tesseract = new TesseractOCR($tempImage);
                        $tesseract->executable('C:\Program Files\Tesseract-OCR\tesseract.exe');
                        $text = $tesseract->run();
                        @unlink($tempImage); // Hapus gambar sementara
                    }
                }
            } else {
                $tesseract = new TesseractOCR($fullPath);
                // On Windows, if tesseract is not in PATH, we must specify the executable path
                $tesseract->executable('C:\Program Files\Tesseract-OCR\tesseract.exe');
                $text = $tesseract->run();
            }
            
            // Basic parsing logic to find No Surat, Perihal, Asal Surat, Tujuan
            $noSurat = '';
            $perihal = '';
            $asalSurat = '';
            $tujuan = '';
            
            $lines = explode("\n", $text);
            
            // Asal surat might be at the top of the letter. Grab the first non-empty string.
            foreach ($lines as $line) {
                $line = trim($line);
                if (!empty($line) && strlen($line) > 5) {
                    $asalSurat = $line;
                    break;
                }
            }

            $isTujuanArea = false;
            $tujuanLines = [];

            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) continue;

                // Match Nomor
                if (preg_match('/^(?:Nomor|No)[\s\.:]+(.+)/i', $line, $matches)) {
                    $val = trim($matches[1]);
                    // Ignore if it's HP/WA
                    if (!preg_match('/HP\/WA/i', $line)) {
                        $noSurat = $val;
                    }
                }

                // Match Perihal
                if (preg_match('/^(?:Perihal|Hal)[\s\.:]+(.+)/i', $line, $matches)) {
                    $perihal = trim($matches[1]);
                }

                // Match Tujuan
                if (!$isTujuanArea && (preg_match('/^Kepada\b/i', $line) || preg_match('/^Yth\.?/i', $line))) {
                    $isTujuanArea = true;
                    // If it also contains Yth, we capture it
                    if (preg_match('/^Yth\.?\s*(.+)/i', $line)) {
                        $tujuanLines[] = $line;
                    } elseif (preg_match('/Kepada\s+(Yth.+)/i', $line, $kepadaMatches)) {
                        $tujuanLines[] = trim($kepadaMatches[1]);
                    }
                    continue;
                }

                if ($isTujuanArea) {
                    if (preg_match('/^di\b/i', $line) || preg_match('/^Di\b/i', $line) || preg_match('/^Assalamu/i', $line) || preg_match('/^Dengan hormat/i', $line)) {
                        $isTujuanArea = false;
                    } else {
                        $tujuanLines[] = $line;
                    }
                }
            }

            if (!empty($tujuanLines)) {
                $tujuan = implode("\n", $tujuanLines);
            }

            $ocrData = [
                'no_surat' => $noSurat ?: ('SM.' . date('Ymd') . '.' . rand(100, 999)),
                'perihal' => $perihal ?: 'Hasil Scan Tidak Jelas (Silakan Edit)',
                'asal_surat' => $asalSurat ?: 'Hasil Scan Tidak Jelas (Silakan Edit)',
                'tujuan' => $tujuan ?: 'Kepala Sekolah',
            ];
            
        } catch (\Exception $e) {
            // Fallback if OCR fails completely
            $ocrData = [
                'no_surat' => 'ERROR_OCR',
                'perihal' => 'Gagal membaca dokumen: ' . substr($e->getMessage(), 0, 100),
                'asal_surat' => 'Silakan ketik manual',
                'tujuan' => 'Kepala Sekolah',
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
            'tujuan' => 'nullable|string|max:255',
        ]);

        IncomingLetter::create([
            'file_path' => $request->file_path,
            'no_surat' => $request->no_surat,
            'perihal' => $request->perihal,
            'asal_surat' => $request->asal_surat,
            'tujuan' => $request->tujuan,
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

    public function destroy(IncomingLetter $incomingLetter)
    {
        // Delete the physical file if it exists
        if ($incomingLetter->file_path && Storage::disk('public')->exists($incomingLetter->file_path)) {
            Storage::disk('public')->delete($incomingLetter->file_path);
        }

        $incomingLetter->delete();

        return redirect()->route('incoming-letters.index')->with('success', 'Surat Masuk berhasil dihapus.');
    }
}
