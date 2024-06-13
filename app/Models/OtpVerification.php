<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtpVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'email', 'otp', 'otp_expires_at', 'verified'
    ];

    protected $dates = [
        'otp_expires_at'
    ];
}
