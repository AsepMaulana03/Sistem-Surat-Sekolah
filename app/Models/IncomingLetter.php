<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncomingLetter extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_path',
        'no_surat',
        'perihal',
        'asal_surat',
        'status', // menunggu_disposisi, selesai
        'instruksi_disposisi',
        'disposisi_at',
        'created_by',
    ];

    protected $casts = [
        'disposisi_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
