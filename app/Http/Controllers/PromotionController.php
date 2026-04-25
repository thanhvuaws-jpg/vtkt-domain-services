<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PromotionController extends Controller
{
    /**
     * Trang Vòng quay may mắn / Nhận quà thành viên mới
     */
    public function index(Request $request)
    {
        if (!$request->hasSession() || !$request->session()->has('users')) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để nhận quà!');
        }

        $user = User::findByUsername($request->session()->get('users'));

        // 1. Lấy Bảng xếp hạng Top Spenders trong tháng (Dữ liệu thật)
        $realSpenders = User::select('users.id', 'users.taikhoan', DB::raw('SUM(orders.price) as total_spent'))
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->where('orders.status', 'active')
            ->whereMonth('orders.created_at', now()->month)
            ->whereYear('orders.created_at', now()->year)
            ->groupBy('users.id', 'users.taikhoan')
            ->orderByDesc('total_spent')
            ->limit(10)
            ->get();

        // 1.1 Lấp đầy 10 người nếu khách thật chưa đủ (Dữ liệu ảo "mềm")
        $topSpenders = $realSpenders->toArray();
        if (count($topSpenders) < 10) {
            $fillerNames = ['Tien_Dung', 'Gia_Huy', 'Minh_Long', 'Phan_An', 'Bao_Tran', 'Ngoc_Diep', 'Hoang_Anh', 'Quoc_Hung', 'Thanh_Mai', 'Kim_Yen', 'Duc_Thinh', 'Huu_Loc'];
            $lastSpent = count($topSpenders) > 0 ? end($topSpenders)['total_spent'] : 5000000;
            
            $countNeeded = 10 - count($topSpenders);
            $usedNames = array_column($topSpenders, 'taikhoan');
            
            for ($i = 0; $i < $countNeeded; $i++) {
                // Chọn tên ngẫu nhiên không trùng với khách thật
                do { $fakeName = $fillerNames[array_rand($fillerNames)]; } while (in_array($fakeName, $usedNames));
                $usedNames[] = $fakeName;

                // Giá tiền ảo giả định (giảm dần)
                $lastSpent -= rand(100000, 300000);
                if ($lastSpent < 50000) $lastSpent = rand(50000, 100000);

                $topSpenders[] = (object)[
                    'id' => 0, // ID giả
                    'taikhoan' => $fakeName,
                    'total_spent' => $lastSpent,
                    'is_fake' => true
                ];
            }
        }
        // Chuyển toàn bộ về object để View xài đồng bộ
        $topSpenders = collect($topSpenders)->map(function($item) {
            return is_array($item) ? (object)$item : $item;
        });

        // 2. Tính tổng chi tiêu của User hiện tại (active orders)
        $userTotalSpent = \App\Models\Order::where('user_id', $user->id)
            ->where('status', 'active')
            ->sum('price');

        // Định nghĩa các mốc thưởng Milestone với dải thưởng ngẫu nhiên
        $milestones = [
            ['id' => 1, 'amount' => 100000,   'min' => 10000,  'max' => 20000,   'reward_text' => 'Voucher 10k - 20k'],
            ['id' => 2, 'amount' => 500000,   'min' => 30000,  'max' => 50000,   'reward_text' => 'Voucher 30k - 50k'],
            ['id' => 3, 'amount' => 1000000,  'min' => 50000,  'max' => 100000,  'reward_text' => 'Voucher 50k - 100k'],
            ['id' => 4, 'amount' => 5000000,  'min' => 200000, 'max' => 500000,  'reward_text' => 'Voucher 200k - 500k'],
            ['id' => 5, 'amount' => 10000000, 'min' => 500000, 'max' => 1500000, 'reward_text' => 'Voucher 500k - 1.5M'],
        ];

        // Kiểm tra xem mốc nào đã được nhận (Claimed)
        foreach ($milestones as &$m) {
            $mCode = "MILESTONE_" . $m['id'] . "_" . $user->id;
            $m['is_claimed'] = \App\Models\Voucher::where('code', 'LIKE', "MS_{$m['id']}_{$user->id}_%")->exists();
        }

        // 3. Lấy danh sách Voucher Chung (Global Vouchers)
        $globalVouchers = \App\Models\Voucher::whereNull('user_id')
            ->where(function($query) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->get();

        // 4. Lấy danh sách Voucher của riêng User này (Kho Voucher)
        $userVouchers = \App\Models\Voucher::where('user_id', $user->id)
            ->orderBy('is_used', 'asc') // Ưu tiên chưa dùng lên trước
            ->orderBy('created_at', 'desc')
            ->get();

        // 5. Kiểm tra giải thưởng Top Spender (Của tháng trước)
        $prevMonth = now()->subMonth();
        $topLastMonth = User::select('users.id', 'users.taikhoan', DB::raw('SUM(orders.price) as total_spent'))
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->where('orders.status', 'active')
            ->whereMonth('orders.created_at', $prevMonth->month)
            ->whereYear('orders.created_at', $prevMonth->year)
            ->groupBy('users.id', 'users.taikhoan')
            ->orderByDesc('total_spent')
            ->limit(3)
            ->get();

        $canClaimTop = null;
        foreach ($topLastMonth as $index => $u) {
            if ($u->id == $user->id) {
                // Kiểm tra xem đã nhận chưa
                $rank = $index + 1;
                $codePrefix = "TOP_REWARD_R{$rank}_{$prevMonth->month}_{$prevMonth->year}";
                $alreadyClaimed = \App\Models\Voucher::where('user_id', $user->id)
                    ->where('code', 'LIKE', "{$codePrefix}_%")
                    ->exists();
                
                if (!$alreadyClaimed) {
                    $canClaimTop = [
                        'rank' => $rank,
                        'month' => $prevMonth->month,
                        'year' => $prevMonth->year,
                        'value' => $rank == 1 ? 500000 : ($rank == 2 ? 300000 : 100000)
                    ];
                }
                break;
            }
        }
        
        return view('pages.promotion.gift', compact('user', 'topSpenders', 'userTotalSpent', 'milestones', 'globalVouchers', 'userVouchers', 'canClaimTop', 'topLastMonth'));
    }

    /**
     * Nhận thưởng Milestone (AJAX) - QUÀ NGẪU NHIÊN
     */
    public function claimMilestone(Request $request)
    {
        if (!$request->hasSession() || !$request->session()->has('users')) {
            return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập!']);
        }

        $user = User::findByUsername($request->session()->get('users'));
        $milestoneId = (int)$request->input('milestone_id');

        $milestones = [
            1 => ['amount' => 100000,   'min' => 10000,  'max' => 20000],
            2 => ['amount' => 500000,   'min' => 30000,  'max' => 50000],
            3 => ['amount' => 1000000,  'min' => 50000,  'max' => 100000],
            4 => ['amount' => 5000000,  'min' => 200000, 'max' => 500000],
            5 => ['amount' => 10000000, 'min' => 500000, 'max' => 1500000],
        ];

        if (!isset($milestones[$milestoneId])) {
            return response()->json(['success' => false, 'message' => 'Mốc thưởng không hợp lệ!']);
        }

        $m = $milestones[$milestoneId];

        // 1. Kiểm tra tổng chi tiêu
        $userTotalSpent = \App\Models\Order::where('user_id', $user->id)
            ->where('status', 'active')
            ->sum('price');

        if ($userTotalSpent < $m['amount']) {
            return response()->json(['success' => false, 'message' => 'Sếp chưa đạt tới mốc này!']);
        }

        // 2. Kiểm tra đã nhận chưa
        $prefix = "MS_{$milestoneId}_{$user->id}_";
        $exists = \App\Models\Voucher::where('user_id', $user->id)
            ->where('code', 'LIKE', "{$prefix}%")
            ->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Sếp đã nhận quà ở mốc này rồi!']);
        }

        // 3. Tính quà ngẫu nhiên
        $winValue = mt_rand($m['min'], $m['max']);
        // Làm tròn tới nghìn
        $winValue = ceil($winValue / 1000) * 1000;

        $voucherCode = $prefix . strtoupper(Str::random(6));

        DB::beginTransaction();
        try {
            Voucher::create([
                'code'       => $voucherCode,
                'value'      => $winValue,
                'user_id'    => $user->id,
                'is_used'    => 0,
                'expires_at' => now()->addMonths(3),
                'mota'       => "Quà tặng Milestone " . number_format($m['amount']) . "đ"
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Chúc mừng! Sếp đã nhận được Voucher trị giá ' . number_format($winValue) . '₫',
                'value'   => $winValue,
                'code'    => $voucherCode
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra, sếp thử lại nhé!']);
        }
    }

    /**
     * Trao giải Top Spenders (Dành cho Admin hoặc Task tự động)
     */
    public function distributeTopRewards(Request $request)
    {
        // Kiểm tra quyền admin nếu cần
        // ...

        $month = now()->month;
        $year = now()->year;

        $top = User::select('users.id', 'users.taikhoan', DB::raw('SUM(orders.price) as total_spent'))
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->where('orders.status', 'active')
            ->whereMonth('orders.created_at', $month)
            ->whereYear('orders.created_at', $year)
            ->groupBy('users.id', 'users.taikhoan')
            ->orderByDesc('total_spent')
            ->limit(3)
            ->get();

        $rewards = [
            0 => ['val' => 500000, 'text' => 'TOP 1 THÁNG'],
            1 => ['val' => 300000, 'text' => 'TOP 2 THÁNG'],
            2 => ['val' => 100000, 'text' => 'TOP 3 THÁNG'],
        ];

        $results = [];
        foreach ($top as $index => $u) {
            $reward = $rewards[$index];
            $code = "TOP_" . ($index + 1) . "_{$month}_{$year}_{$u->id}";
            
            // Check exists
            if (!Voucher::where('code', $code)->exists()) {
                Voucher::create([
                    'code' => $code,
                    'value' => $reward['val'],
                    'user_id' => $u->id,
                    'is_used' => 0,
                    'expires_at' => now()->addMonths(1),
                    'mota' => "Giải thưởng {$reward['text']} {$month}/{$year}"
                ]);
                $results[] = "Đã trao giải cho {$u->taikhoan} ({$reward['text']})";
            }
        }

        return response()->json(['success' => true, 'results' => $results]);
    }

    /**
     * Nhận thưởng Top Spender (AJAX) - DÀNH CHO TOP 1, 2, 3 THÁNG TRƯỚC
     */
    public function claimTopReward(Request $request)
    {
        if (!$request->hasSession() || !$request->session()->has('users')) {
            return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập!']);
        }

        $user = User::findByUsername($request->session()->get('users'));
        $prevMonth = now()->subMonth();
        
        // 1. Lấy danh sách Top 3 tháng trước
        $topLastMonth = User::select('users.id', 'users.taikhoan', DB::raw('SUM(orders.price) as total_spent'))
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->where('orders.status', 'active')
            ->whereMonth('orders.created_at', $prevMonth->month)
            ->whereYear('orders.created_at', $prevMonth->year)
            ->groupBy('users.id', 'users.taikhoan')
            ->orderByDesc('total_spent')
            ->limit(3)
            ->get();

        $rank = 0;
        foreach ($topLastMonth as $index => $u) {
            if ($u->id == $user->id) {
                $rank = $index + 1;
                break;
            }
        }

        if ($rank == 0) {
            return response()->json(['success' => false, 'message' => 'Rất tiếc, sếp không nằm trong Top 3 tháng trước!']);
        }

        // 2. Kiểm tra xem đã nhận chưa
        $codePrefix = "TOP_REWARD_R{$rank}_{$prevMonth->month}_{$prevMonth->year}";
        $alreadyClaimed = \App\Models\Voucher::where('user_id', $user->id)
            ->where('code', 'LIKE', "{$codePrefix}_%")
            ->exists();

        if ($alreadyClaimed) {
            return response()->json(['success' => false, 'message' => 'Sếp đã nhận thưởng cho hạng ' . $rank . ' rồi!']);
        }

        // 3. Trao quà
        $rewardValue = $rank == 1 ? 500000 : ($rank == 2 ? 300000 : 100000);
        $voucherCode = $codePrefix . "_" . strtoupper(Str::random(6));

        DB::beginTransaction();
        try {
            Voucher::create([
                'code' => $voucherCode,
                'value' => $rewardValue,
                'user_id' => $user->id,
                'is_used' => 0,
                'expires_at' => now()->addMonths(1),
                'mota' => "Giải thưởng TOP {$rank} tháng {$prevMonth->month}/{$prevMonth->year}"
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Chúc mừng! Sếp đã nhận được Voucher TOP ' . $rank . ' trị giá ' . number_format($rewardValue) . '₫',
                'code' => $voucherCode
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra, sếp thử lại nhé!']);
        }
    }

    /**
     * Thuật toán bốc thăm theo trọng số
     */
    private function getRandomPrize($prizes)
    {
        $totalWeight = array_sum(array_column($prizes, 'weight'));
        $rand = mt_rand(1, $totalWeight);
        $currentWeight = 0;

        foreach ($prizes as $prize) {
            $currentWeight += $prize['weight'];
            if ($rand <= $currentWeight) {
                return $prize;
            }
        }
        return $prizes[0];
    }
}
