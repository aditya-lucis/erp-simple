<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalHeader extends Model
{
    use HasFactory;

    protected $fillable = [
        'journal_code',
        'journal_date',
        'description'
    ];

    protected $casts = [
        'journal_date' => 'date'
    ];

    public function details()
    {
        return $this->hasMany(JournalDetail::class);
    }
}
