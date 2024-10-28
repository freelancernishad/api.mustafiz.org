<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donner extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name', 'last_name', 'email', 'phone',
        'address', 'address_line_2', 'city', 'country',
        'zip', 'payment_type'
    ];

    public function donationPayments()
    {
        return $this->hasMany(DonationPayment::class);
    }
}
