<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'decision_id',
        'currency',
        'amount',
        'payment_by',
        'datetime',
        'note',
    ];

    /**
     * Get the decision that owns the transaction.
     */
    public function decision()
    {
        return $this->belongsTo(Decision::class);
    }
}
