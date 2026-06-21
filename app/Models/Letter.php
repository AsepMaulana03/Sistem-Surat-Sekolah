<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Letter extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'type_code',
        'letter_number',
        'event_name',
        'letter_date',
        'destination',
        'content',
        'rejection_note',
        'reviewed_at',
        'reviewed_by',
        'status', // draft, pending, approved, rejected
    ];

    protected $casts = [
        'letter_date' => 'date',
        'reviewed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
