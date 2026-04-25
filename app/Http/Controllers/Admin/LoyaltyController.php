<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoyaltyController extends Controller
{
    /**
     * Quản lý Bảng xếp hạng & Milestone
     */
    public function index()
    {
        // 1. Lấy danh sách Top Spender thực tế (Tháng hiện tại)
        $topSpenders = User::select('users.id', 'users.taikhoan', DB::raw('SUM(orders.price) as total_spent'))
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->where('orders.status', 'active')
            ->whereMonth('orders.created_at', now()->month)
            ->whereYear('orders.created_at', now()->year)
            ->groupBy('users.id', 'users.taikhoan')
            ->orderByDesc('total_spent')
            ->limit(20)
            ->get();

        // 2. Thống kê Voucher thưởng Top tháng trước
        $lastMonth = now()->subMonth();
        $topVouchers = Voucher::where('code', 'LIKE', 'TOP_REWARD_%')
            ->whereYear('created_at', now()->year)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.loyalty.index', compact('topSpenders', 'topVouchers'));
    }
}
