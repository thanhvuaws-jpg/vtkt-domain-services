<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoucherUsage extends Model
{
    protected $table = 'voucher_usages';
    
    protected $fillable = [
        'user_id',
        'voucher_id',
        'created_at',
    ];

    public $timestamps = false; // Bảng này dùng created_at mặc định của DB
}
