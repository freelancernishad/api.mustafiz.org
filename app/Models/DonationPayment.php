<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonationPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'donner_id', 'trx_id', 'amount', 'currency',
        'status', 'date', 'month', 'year', 'payment_url',
        'ipn_response', 'method', 'checkout_session_id'
    ];

    public function donner()
    {
        return $this->belongsTo(Donner::class);
    }
}
