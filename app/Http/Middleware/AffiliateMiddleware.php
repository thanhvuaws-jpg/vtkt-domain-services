<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AffiliateMiddleware
{
    /**
     * Ghi nhận người giới thiệu từ URL (?ref=ID)
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->has('ref')) {
            $refId = $request->query('ref');
            
            // Lưu vào session trong 30 ngày (nếu Laravel session hỗ trợ lifetime)
            // Hoặc đơn giản là lưu vào session hiện tại
            $request->session()->put('referrer_id', $refId);
        }

        return $next($request);
    }
}
