<?php

namespace App\Http\Controllers;

use App\Models\Template;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    public function index()
    {
        $templates = Template::all();
        return view('templates.index', compact('templates'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Generate next code
        $lastCode = Template::max('code');
        $nextCode = $lastCode ? str_pad((int)$lastCode + 1, 2, '0', STR_PAD_LEFT) : '01';

        Template::create([
            'code' => $nextCode,
            'name' => $validated['name'],
            'content' => '', // Default empty content
        ]);

        return redirect()->route('templates.index')->with('success', 'Template berhasil ditambahkan');
    }
}
