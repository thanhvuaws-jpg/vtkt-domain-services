<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\Domain;
use App\Models\Settings;
use App\Models\User;
use App\Services\OrderService;
use App\Services\OrderStrategyFactory;
use App\Services\BankingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class AjaxController
 * Controller xử lý các AJAX request từ frontend
 *
 * REFACTORED:
 * - Xóa private generateMGD() → dùng OrderHelper::generateMGD() bên trong OrderService
 * - Xóa buyDomain/buyHosting/buyVPS/buySourceCode (640 dòng duplicate)
 * - Thêm 1 hàm buy() gọi OrderService::placeOrder()
 * - Backward-compat: 4 hàm cũ vẫn hoạt động qua buy()
 */
class AjaxController extends Controller
{
    protected $orderService;
    protected $bankingService;

    public function __construct(OrderService $orderService, BankingService $bankingService)
    {
        $this->orderService  = $orderService;
        $this->bankingService = $bankingService;
    }

    /**
     * Lấy thông tin thanh toán (QR Code, STK...) động
     */
    public function getPaymentDetails(Request $request)
    {
        if (!$request->hasSession() || !$request->session()->has('users')) {
            return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập!']);
        }

        $user = User::findByUsername($request->session()->get('users'));
        $method = strtoupper($request->input('method', 'BANKING'));
        $amount = (int)$request->input('amount', 50000);

        if ($amount < 10000) {
            return response()->json(['success' => false, 'message' => 'Số tiền nạp tối thiểu là 10,000₫']);
        }

        $strategy = $this->bankingService->getStrategy($method);
        if (!$strategy) {
            return response()->json(['success' => false, 'message' => 'Phương thức nạp không hỗ trợ']);
        }

        // Tạo mã nội dung chuyển khoản: Admin_VUDZ + ID User
        $content = "Admin_VUDZ{$user->id}";
        
        $details = $strategy->getPaymentDetails($amount, $content);

        return response()->json([
            'success' => true,
            'details' => $details
        ]);
    }

    /**
     * Áp dụng Voucher giảm giá
     */
    public function applyVoucher(Request $request)
    {
        if (!$request->hasSession() || !$request->session()->has('users')) {
            return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập!']);
        }

        $code = trim($request->input('code', ''));
        $total = (int)$request->input('total', 0);
        $user = User::findByUsername($request->session()->get('users'));

        if (!$code) {
            return response()->json(['success' => false, 'message' => 'Vui lòng nhập mã Voucher']);
        }

        $voucher = \App\Models\Voucher::where('code', $code)
            ->where(function($query) use ($user) {
                $query->whereNull('user_id')->orWhere('user_id', $user->id);
            })
            ->first();

        if (!$voucher) {
            return response()->json(['success' => false, 'message' => 'Mã Voucher không tồn tại hoặc không thuộc sở hữu của bạn']);
        }

        // Kiểm tra hết hạn
        if ($voucher->expires_at && \Carbon\Carbon::parse($voucher->expires_at)->isPast()) {
            return response()->json(['success' => false, 'message' => 'Mã Voucher đã hết hạn']);
        }

        // Kiểm tra trạng thái đã sử dụng
        if (is_null($voucher->user_id)) {
            // VOUCHER CHUNG (Global) - Kiểm tra trong bảng usages
            $alreadyUsed = \App\Models\VoucherUsage::where('user_id', $user->id)
                ->where('voucher_id', $voucher->id)
                ->exists();
            if ($alreadyUsed) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn đã sử dụng mã giảm giá này cho một đơn hàng trước đó rồi!'
                ]);
            }
        } else {
            // VOUCHER CÁ NHÂN - Kiểm tra cột is_used truyền thống
            if ($voucher->is_used) {
                return response()->json(['success' => false, 'message' => 'Mã Voucher này đã được sử dụng rồi']);
            }
        }

        $discount = $voucher->value;
        $newTotal = max(0, $total - $discount);

        return response()->json([
            'success'   => true,
            'message'   => 'Áp dụng mã giảm giá thành công!',
            'discount'  => $discount,
            'new_total' => $newTotal,
            'formatted_discount' => number_format($discount) . 'đ',
            'formatted_total'    => number_format($newTotal) . 'đ'
        ]);
    }

    // =========================================================================
    // KIỂM TRA DOMAIN (WHOIS)
    // =========================================================================

    /**
     * Kiểm tra domain có sẵn không (WHOIS check)
     */
    public function checkDomain(Request $request)
    {
        $tenmien = strtolower(trim($request->input('name', '')));
        $domainSuffix = $request->input('domain', '');
        $ok = $tenmien . $domainSuffix;

        $supported = Domain::all()->pluck('duoi')->map(fn($d) => strtolower($d))->toArray();

        if ($tenmien === '') {
            return response()->json([
                'success' => false,
                'message' => 'Vui Lòng Nhập Tên Miền',
                'html'    => '<script>toastr.error("Vui Lòng Nhập Tên Miền", "Thông Báo");</script>'
            ]);
        }

        if (!in_array(strtolower($domainSuffix), $supported, true)) {
            return response()->json([
                'success' => false,
                'message' => "Đuôi Miền {$domainSuffix} Không Hỗ Trợ!",
                'html'    => '<script>toastr.error("Đuôi Miền ' . $domainSuffix . ' Không Hỗ Trợ! ", "Thông Báo");</script>'
            ]);
        }

        $labelRegex = '/^(?!-)[a-z0-9-]{1,63}(?<!-)$/';
        $labels = explode('.', $tenmien);
        foreach ($labels as $label) {
            if ($label === '' || !preg_match($labelRegex, $label)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tên miền không hợp lệ (chỉ chữ, số, gạch ngang; không bắt đầu/kết thúc bằng -)',
                    'html'    => '<script>toastr.error("Tên miền không hợp lệ", "Thông Báo");</script>'
                ]);
            }
        }

        if (strlen($ok) > 253) {
            return response()->json([
                'success' => false,
                'message' => 'Tên miền quá dài',
                'html'    => '<script>toastr.error("Tên miền quá dài", "Thông Báo");</script>'
            ]);
        }

        $hasARecord   = checkdnsrr($ok, 'A');
        $pingResult   = $this->checkDomainByPing($ok);
        $whoisResult  = $this->checkWhoisVietnam($ok);

        $strongEvidence = 0;
        if ($hasARecord)          $strongEvidence++;
        if ($pingResult === true) $strongEvidence++;
        if ($whoisResult === true) $strongEvidence++;

        if ($strongEvidence >= 2) {
            return response()->json([
                'success' => false,
                'message' => "Tên Miền {$ok} Đã Được Đăng Ký!",
                'html'    => '<script>toastr.error("Tên Miền ' . $ok . ' Đã Được Đăng Ký!", "Thông Báo");</script>'
            ]);
        }

        $checkoutUrl = route('domain.checkout', ['domain' => $ok]);
        $html = '<script>toastr.success("Bạn Có Thể Mua Miền ' . $ok . ' Ngay Bây Giờ", "Thông Báo");</script>';
        $html .= '<center><b class="text-danger">Bạn Có Thể Đăng Ký Tên Miền Này Ngay Bây Giờ <a href="' . $checkoutUrl . '" class="text-success">Tại Đây</a></b><br><br></center>';

        return response()->json([
            'success' => true,
            'message' => "Bạn Có Thể Mua Miền {$ok} Ngay Bây Giờ",
            'html'    => $html
        ]);
    }

    private function checkDomainByPing($domain)
    {
        $ip = gethostbyname($domain);
        if ($ip === $domain) return false;

        $excludeIPs = [
            '127.0.0.1', '0.0.0.0', '8.8.8.8', '1.1.1.1', '208.67.222.222',
            '74.125.224.72', '173.194.44.0', '216.58.192.0', '104.21.0.0',
            '172.67.0.0', '141.101.0.0', '162.158.0.0', '198.41.0.0', '188.114.0.0',
        ];

        foreach ($excludeIPs as $excludeIP) {
            if ($ip === $excludeIP || strpos($ip, substr($excludeIP, 0, 8)) === 0) {
                return false;
            }
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return false;
        }

        $majorWebsiteIPs = [
            '142.250.0.0', '157.240.0.0', '31.13.0.0', '66.220.0.0', '69.63.0.0',
            '104.244.0.0', '151.101.0.0', '13.107.0.0', '52.84.0.0', '104.16.0.0',
            '172.64.0.0', '198.41.0.0',
        ];

        foreach ($majorWebsiteIPs as $majorIP) {
            if (strpos($ip, substr($majorIP, 0, 8)) === 0) {
                return true;
            }
        }

        return false;
    }

    private function checkWhoisVietnam($domain)
    {
        $url = "https://domain.inet.vn/api/whois?domain=" . urlencode($domain);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
        $check = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode != 200 || !$check) return null;

        $checkLower = strtolower($check);

        $registeredPhrases = [
            'registry expiry date:', 'expiration date:', 'registration date:',
            'created:', 'registrar:', 'domain status: ok', 'domain status: active',
        ];
        foreach ($registeredPhrases as $phrase) {
            if (strpos($checkLower, $phrase) !== false) return true;
        }

        $availablePhrases = [
            'no match', 'not found', 'no data found', 'không tìm thấy',
            'chưa được đăng ký', 'domain not found', 'no entries found',
        ];
        foreach ($availablePhrases as $phrase) {
            if (strpos($checkLower, $phrase) !== false) return false;
        }

        return null;
    }

    // =========================================================================
    // MUA HÀNG - 1 hàm thay thế 4 buyXxx cũ
    // =========================================================================

    /**
     * Mua hàng AJAX - UNIFIED endpoint
     * Thay thế buyDomain/buyHosting/buyVPS/buySourceCode (640 dòng duplicate)
     */
    public function buy(Request $request)
    {
        if (!$request->hasSession() || !$request->session()->has('users')) {
            return response()->json([
                'success' => false,
                'message' => 'Vui Lòng Đăng Nhập Để Thực Hiện!',
                'html'    => '<script>toastr.error("Vui Lòng Đăng Nhập!", "Thông Báo");</script>'
            ]);
        }

        $type = $request->input('type', '');

        if (!OrderStrategyFactory::isSupported($type)) {
            return response()->json([
                'success' => false,
                'message' => 'Loại sản phẩm không hợp lệ!'
            ]);
        }

        $user = User::findByUsername($request->session()->get('users'));
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin tài khoản!',
                'html'    => '<script>toastr.error("Không tìm thấy tài khoản!", "Thông Báo");</script>'
            ]);
        }

        [$productId, $options] = match($type) {
            'domain'     => [0, [
                'domain'  => $request->input('domain', ''),
                'ns1'     => $request->input('ns1', ''),
                'ns2'     => $request->input('ns2', ''),
                'hsd'     => $request->input('hsd', '1'),
                'voucher' => $request->input('voucher'),
            ]],
            'hosting'    => [(int)$request->input('hosting_id', 0), [
                'period'  => $request->input('period', 'month'),
                'voucher' => $request->input('voucher'),
            ]],
            'vps'        => [(int)$request->input('vps_id', 0), [
                'period'  => $request->input('period', 'month'),
                'voucher' => $request->input('voucher'),
            ]],
            'sourcecode' => [(int)$request->input('source_code_id', 0), [
                'voucher' => $request->input('voucher'),
            ]],
            default      => [0, []]
        };

        $result = $this->orderService->placeOrder($type, $productId, $user->id, $options);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
                'html'    => '<script>toastr.error("' . addslashes($result['message']) . '", "Thông Báo");</script>'
            ]);
        }

        $mgd = $result['mgd'];
        $redirect = match($type) {
            'sourcecode' => route('download.index', ['mgd' => $mgd]),
            'hosting'    => route('contact-admin', ['type' => 'hosting', 'mgd' => $mgd]),
            'vps'        => route('contact-admin', ['type' => 'vps', 'mgd' => $mgd]),
            default      => null
        };

        $html = '<script>toastr.success("' . addslashes($result['message']) . '", "Thông Báo");</script>'
              . ($redirect ? '<script>setTimeout(function(){ window.location.href = "' . $redirect . '"; }, 1500);</script>' : '');

        return response()->json([
            'success'  => true,
            'message'  => $result['message'],
            'html'     => $html,
            'redirect' => $redirect,
        ]);
    }

    // Backward-compat wrappers
    public function buyDomain(Request $request)     { return $this->buy($request->merge(['type' => 'domain'])); }
    public function buyHosting(Request $request)    { return $this->buy($request->merge(['type' => 'hosting'])); }
    public function buyVPS(Request $request)        { return $this->buy($request->merge(['type' => 'vps'])); }
    public function buySourceCode(Request $request) { return $this->buy($request->merge(['type' => 'sourcecode'])); }

    // =========================================================================
    // CẬP NHẬT DNS
    // =========================================================================

    /**
     * Cập nhật DNS cho domain (chu kỳ 15 ngày)
     */
    public function updateDns(Request $request)
    {
        if (!$request->hasSession() || !$request->session()->has('users')) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập!',
                'html'    => '<script>toastr.error("Vui lòng đăng nhập!", "Thông Báo");</script>'
            ]);
        }

        $ns1 = $request->input('ns1');
        $ns2 = $request->input('ns2');
        $mgd = $request->input('mgd');

        $user = User::findByUsername($request->session()->get('users'));
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin người dùng!',
                'html'    => '<script>toastr.error("Không tìm thấy thông tin người dùng!", "Thông Báo");</script>'
            ]);
        }

        $checkmgd = \App\Models\Order::where('user_id', $user->id)
            ->where('mgd', $mgd)
            ->where('product_type', 'domain')
            ->first();

        if (!$checkmgd) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền quản lý tên miền này!',
                'html'    => '<script>toastr.error("Bạn không có quyền quản lý tên miền này!", "Thông Báo");</script>'
            ]);
        }

        if ($ns1 == "" || $ns2 == "") {
            return response()->json([
                'success' => false,
                'message' => 'Vui Lòng Nhập Đầy Đủ Thông Tin!',
                'html'    => '<script>toastr.error("Vui Lòng Nhập Đầy Đủ Thông Tin!", "Thông Báo");</script>'
            ]);
        }

        if ($checkmgd->timedns == '0') {
            $checkmgd->ns1     = $ns1;
            $checkmgd->ns2     = $ns2;
            $checkmgd->timedns = date('d/m/Y');
            $checkmgd->save();

            return response()->json([
                'success' => true,
                'message' => 'Thay Đổi DNS Thành Công, Vui Lòng Chờ 12h - 24h Để DNS Mới Hoạt Động',
                'html'    => '<script>toastr.success("Thay Đổi DNS Thành Công, Vui Lòng Chờ 12h - 24h Để DNS Mới Hoạt Động", "Thông Báo");</script>'
            ]);
        } else {
            try {
                $lastUpdateDate = Carbon::createFromFormat('d/m/Y', $checkmgd->timedns);
                $today = Carbon::now();
                $daysDiff = $today->diffInDays($lastUpdateDate, false);

                if ($daysDiff < 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Dữ Liệu Ngày Cập Nhật Không Hợp Lệ. Vui Lòng Liên Hệ Admin!',
                        'html'    => '<script>toastr.error("Dữ Liệu Ngày Cập Nhật Không Hợp Lệ!", "Thông Báo");</script>'
                    ]);
                }

                if ($daysDiff >= 15) {
                    $checkmgd->ns1     = $ns1;
                    $checkmgd->ns2     = $ns2;
                    $checkmgd->timedns = date('d/m/Y');
                    $checkmgd->save();

                    return response()->json([
                        'success' => true,
                        'message' => 'Thay Đổi DNS Thành Công, Vui Lòng Chờ 12h - 24h Để DNS Mới Hoạt Động',
                        'html'    => '<script>toastr.success("Thay Đổi DNS Thành Công, Vui Lòng Chờ 12h - 24h Để DNS Mới Hoạt Động", "Thông Báo");</script>'
                    ]);
                }

                $daysRemaining = 15 - $daysDiff;
                return response()->json([
                    'success' => false,
                    'message' => "Bạn Không Thể Cập Nhật DNS Ngay Bây Giờ! Vui Lòng Đợi Thêm {$daysRemaining} Ngày Nữa.",
                    'html'    => "<script>toastr.error('Bạn Không Thể Cập Nhật DNS Ngay Bây Giờ! Còn {$daysRemaining} Ngày Nữa.', 'Thông Báo');</script>"
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lỗi Xử Lý Ngày Tháng, Vui Lòng Liên Hệ Admin!',
                    'html'    => '<script>toastr.error("Lỗi Xử Lý Ngày Tháng!", "Thông Báo");</script>'
                ]);
            }
        }
    }

}
