<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

class Decision extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'why',
        'how_long',
        'how_much',
        'note',
        'status',
        'approved_amount',
        'feedback',
        'date',
        'currency',
        'start_date',
        'end_date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function getDateAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('m-d-Y');
    }

    public function getStartDateAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('m-d-Y');
    }

    public function getEndDateAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('m-d-Y');
    }
}
