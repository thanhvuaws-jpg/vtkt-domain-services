<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    /**
     * Tên bảng trong CSDL
     */
    protected $table = 'orders';

    /**
     * Các trường cho phép mass assignment (không khóa trường nào)
     */
    protected $guarded = ['id'];

    /**
     * Tự động ép kiểu dữ liệu khi lấy từ Database ra
     */
    protected $casts = [
        'options' => 'array',  // Tự động parse JSON string ra mảng
        'status' => 'integer',
        'price' => 'integer',
        'product_id' => 'integer'
    ];

    /**
     * Quan hệ: 1 Đơn hàng thuộc về 1 User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Quan hệ linh hoạt: Lấy thông tin sản phẩm gốc dựa vào product_type
     * Trả về model của Hosting/VPS/SourceCode. Với Domain trả về null (vì thông tin đã nằm hết ở custom options).
     */
    public function product()
    {
        return match ($this->product_type) {
            'hosting'    => Hosting::find($this->product_id),
            'vps'        => VPS::find($this->product_id),
            'sourcecode' => SourceCode::find($this->product_id),
            default      => null,
        };
    }

    // --- MAGIC ACCESSORS DÀNH CHO VIEW CŨ (TƯƠNG THÍCH NGƯỢC) ---

    public function getUidAttribute()
    {
        return $this->user_id;
    }

    public function getDomainAttribute()
    {
        return $this->options['domain'] ?? null;
    }

    public function getNs1Attribute()
    {
        return $this->options['ns1'] ?? null;
    }

    public function getNs2Attribute()
    {
        return $this->options['ns2'] ?? null;
    }

    public function getHsdAttribute()
    {
        return $this->options['hsd'] ?? null;
    }

    public function getPeriodAttribute()
    {
        return $this->options['period'] ?? null;
    }

    // --- MUTATORS (SETTERS) CHO PHÉP UPDATE JSON NGẦM ---
    public function setDomainAttribute($value) { $this->updateOption('domain', $value); }
    public function setNs1Attribute($value) { $this->updateOption('ns1', $value); }
    public function setNs2Attribute($value) { $this->updateOption('ns2', $value); }
    public function setHsdAttribute($value) { $this->updateOption('hsd', $value); }
    public function setPeriodAttribute($value) { $this->updateOption('period', $value); }
    public function setTimednsAttribute($value) { $this->updateOption('timedns', $value); }
    public function setAhihiAttribute($value) { $this->updateOption('ahihi', $value); }

    protected function updateOption($key, $value)
    {
        $options = $this->options ?? [];
        $options[$key] = $value;
        $this->attributes['options'] = json_encode($options);
    }
}
