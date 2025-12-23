<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'journal_header_id',
        'account_id',
        'position',
        'amount'
    ];

    public function journal()
    {
        return $this->belongsTo(JournalHeader::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
