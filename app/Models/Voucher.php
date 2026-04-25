<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $table = 'vouchers';

    protected $fillable = [
        'code',
        'value',
        'user_id',
        'is_used',
        'expires_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_used' => 'boolean',
    ];

    /**
     * Voucher thuộc về một User (định danh cho khách mới bốc thăm)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
