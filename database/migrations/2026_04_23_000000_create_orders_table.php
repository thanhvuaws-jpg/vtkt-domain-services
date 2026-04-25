<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('orders'); // Xóa trước nếu đã tồn tại dở dang

        // 1. Tạo bảng orders mới
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Khóa ngoại tới user
            $table->string('product_type', 50); // 'domain', 'hosting', 'vps', 'sourcecode'
            $table->unsignedBigInteger('product_id')->default(0); // 0 với domain
            $table->string('mgd', 100)->unique();
            $table->tinyInteger('status')->default(0); // 0=chờ duyệt, 1=đã duyệt, 4=từ chối
            $table->integer('price')->default(0);
            $table->json('options')->nullable(); // Lưu các thông tin linh hoạt
            $table->string('time', 50)->nullable(); // Định dạng thời gian cũ: d/m/Y - H:i:s
            $table->timestamps();

            $table->index('user_id');
            $table->index(['product_type', 'product_id']);
            
            // Xóa user tự động xóa đơn hàng (Comment lại để tránh lỗi Schema mismatch trên SQL cũ)
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // 2. Chép dữ liệu từ các bảng cũ sang
        
        // --- CHÉP DOMAIN (từ bảng: history) ---
        if (Schema::hasTable('history')) {
            $domains = DB::table('history')->get();
            $inserts = [];
            foreach ($domains as $d) {
                $inserts[] = [
                    'user_id'      => $d->uid,
                    'product_type' => 'domain',
                    'product_id'   => 0,
                    'mgd'          => $d->mgd ?? ('MGD' . time() . rand(100, 999)),
                    'status'       => $d->status ?? 0,
                    'price'        => 0, // Domain cũ không lưu giá tại lúc mua
                    'options'      => json_encode([
                        'domain'  => $d->domain,
                        'ns1'     => $d->ns1,
                        'ns2'     => $d->ns2,
                        'hsd'     => $d->hsd,
                        'timedns' => $d->timedns,
                        'ahihi'   => $d->ahihi
                    ]),
                    'time'         => $d->time,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ];
            }
            if (count($inserts) > 0) {
                // Chia nhỏ chunk 500 records mỗi lần insert để tránh sập RAM
                foreach (array_chunk($inserts, 500) as $chunk) {
                    DB::table('orders')->insert($chunk);
                }
            }
        }

        // --- CHÉP HOSTING (từ bảng: hostinghistory) ---
        if (Schema::hasTable('hostinghistory')) {
            $hostings = DB::table('hostinghistory')->get();
            $inserts = [];
            foreach ($hostings as $h) {
                $inserts[] = [
                    'user_id'      => $h->uid,
                    'product_type' => 'hosting',
                    'product_id'   => $h->hosting_id ?? 0,
                    'mgd'          => $h->mgd ?? ('MGD' . time() . rand(100, 999)),
                    'status'       => $h->status ?? 0,
                    'price'        => 0,
                    'options'      => json_encode([
                        'period' => $h->period
                    ]),
                    'time'         => $h->time,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ];
            }
            if (count($inserts) > 0) {
                foreach (array_chunk($inserts, 500) as $chunk) {
                    DB::table('orders')->insert($chunk);
                }
            }
        }

        // --- CHÉP VPS (từ bảng: vpshistory) ---
        if (Schema::hasTable('vpshistory')) {
            $vpss = DB::table('vpshistory')->get();
            $inserts = [];
            foreach ($vpss as $v) {
                $inserts[] = [
                    'user_id'      => $v->uid,
                    'product_type' => 'vps',
                    'product_id'   => $v->vps_id ?? 0,
                    'mgd'          => $v->mgd ?? ('MGD' . time() . rand(100, 999)),
                    'status'       => $v->status ?? 0,
                    'price'        => 0,
                    'options'      => json_encode([
                        'period' => $v->period
                    ]),
                    'time'         => $v->time,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ];
            }
            if (count($inserts) > 0) {
                foreach (array_chunk($inserts, 500) as $chunk) {
                    DB::table('orders')->insert($chunk);
                }
            }
        }

        // --- CHÉP SOURCE CODE (từ bảng: sourcecodehistory) ---
        if (Schema::hasTable('sourcecodehistory')) {
            $srcs = DB::table('sourcecodehistory')->get();
            $inserts = [];
            foreach ($srcs as $s) {
                $inserts[] = [
                    'user_id'      => $s->uid,
                    'product_type' => 'sourcecode',
                    'product_id'   => $s->source_code_id ?? 0,
                    'mgd'          => $s->mgd ?? ('MGD' . time() . rand(100, 999)),
                    'status'       => $s->status ?? 0,
                    'price'        => 0,
                    'options'      => json_encode([]),
                    'time'         => $s->time,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ];
            }
            if (count($inserts) > 0) {
                foreach (array_chunk($inserts, 500) as $chunk) {
                    DB::table('orders')->insert($chunk);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
