<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Decision extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'why',
        'how_long',
        'how_much',
        'note',
        'status',
    ];

    /**
     * Get the user that owns the decision.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
