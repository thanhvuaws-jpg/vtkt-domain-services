<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    protected $table = 'deposits';

    protected $fillable = [
        'code',
        'amount',
        'user_id',
        'status'
    ];

    /**
     * Nạp tiền thuộc về một User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
