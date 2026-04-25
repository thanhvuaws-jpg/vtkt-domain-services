<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Models\Hosting;
use App\Models\VPS;
use App\Models\SourceCode;
use App\Models\User;
use App\Services\OrderService;
use App\Services\OrderStrategyFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class CheckoutController
 * Controller xử lý trang checkout và thanh toán đơn hàng
 *
 * Đã refactor: 4 hàm processDomain/processHosting/processVPS/processSourceCode
 * được gộp thành 1 hàm processOrder() duy nhất dùng OrderService.
 * Khi thêm sản phẩm mới, KHÔNG cần sửa file này.
 */
class CheckoutController extends Controller
{
    protected OrderService $orderService;

    /**
     * Constructor - Inject OrderService (thay thế TelegramService inject trực tiếp)
     * OrderService đã tự inject TelegramService bên trong
     */
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    // =========================================================================
    // TRANG CHECKOUT (Hiển thị giao diện)
    // =========================================================================

    /**
     * Hiển thị trang checkout domain
     */
    public function domain(Request $request)
    {
        $domainName = $request->query('domain');

        if (empty($domainName)) {
            return redirect()->route('home');
        }

        $parts = explode('.', $domainName);
        if (count($parts) < 2) {
            return redirect()->route('home');
        }

        $extension = '.' . $parts[1];
        $domain = Domain::findByDuoi($extension);

        if (!$domain) {
            return redirect()->route('home');
        }

        return view('pages.checkout.domain', [
            'domainName' => $domainName,
            'domain'     => $domain,
            'price'      => $domain->price,
        ]);
    }

    /**
     * Hiển thị trang checkout hosting
     */
    public function hosting(Request $request)
    {
        if (!session()->has('users')) {
            return redirect()->route('login');
        }

        $id = $request->query('id', 0);
        if ($id == 0) {
            return redirect()->route('hosting.index');
        }

        $hosting = Hosting::find($id);
        if (!$hosting) {
            return redirect()->route('hosting.index');
        }

        return view('pages.checkout.hosting', ['hosting' => $hosting]);
    }

    /**
     * Hiển thị trang checkout VPS
     */
    public function vps(Request $request)
    {
        if (!session()->has('users')) {
            return redirect()->route('login');
        }

        $id = $request->query('id', 0);
        if ($id == 0) {
            return redirect()->route('vps.index');
        }

        $vps = VPS::find($id);
        if (!$vps) {
            return redirect()->route('vps.index');
        }

        return view('pages.checkout.vps', ['vps' => $vps]);
    }

    /**
     * Hiển thị trang checkout Source Code
     */
    public function sourcecode(Request $request)
    {
        if (!session()->has('users')) {
            return redirect()->route('login');
        }

        $id = $request->query('id', 0);
        if ($id == 0) {
            return redirect()->route('sourcecode.index');
        }

        $sourceCode = SourceCode::find($id);
        if (!$sourceCode) {
            return redirect()->route('sourcecode.index');
        }

        return view('pages.checkout.sourcecode', ['sourceCode' => $sourceCode]);
    }

    // =========================================================================
    // XỬ LÝ MUA HÀNG - 1 hàm duy nhất thay thế 4 hàm cũ
    // =========================================================================

    /**
     * Xử lý mua hàng AJAX - UNIFIED (thay thế 4 hàm riêng lẻ)
     *
     * Routes sẽ gọi:
     *   POST /checkout/process/domain     → processOrder($request, 'domain')
     *   POST /checkout/process/hosting    → processOrder($request, 'hosting')
     *   POST /checkout/process/vps        → processOrder($request, 'vps')
     *   POST /checkout/process/sourcecode → processOrder($request, 'sourcecode')
     *
     * @param Request $request  HTTP request
     * @param string  $type     Loại sản phẩm ('domain', 'hosting', 'vps', 'sourcecode')
     */
    public function processOrder(Request $request, string $type)
    {
        // Kiểm tra session
        if (!$request->hasSession()) {
            return response()->json([
                'success' => false,
                'message' => 'Phiên làm việc đã hết hạn. Vui lòng tải lại trang!',
            ]);
        }

        // Kiểm tra đăng nhập
        if (!session()->has('users')) {
            return response()->json([
                'success' => false,
                'message' => 'Vui Lòng Đăng Nhập Để Thực Hiện!',
                'html'    => '<script>toastr.error("Vui Lòng Đăng Nhập!", "Thông Báo"); setTimeout(function(){window.location.href="' . route('login') . '";},2000);</script>',
            ]);
        }

        // Kiểm tra product type hợp lệ
        if (!OrderStrategyFactory::isSupported($type)) {
            return response()->json([
                'success' => false,
                'message' => 'Loại sản phẩm không hợp lệ!',
            ]);
        }

        // Lấy user hiện tại
        $user = User::findByUsername(session('users'));
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin tài khoản!',
            ]);
        }

        // Xác định product_id và options theo từng loại sản phẩm
        [$productId, $options] = $this->extractOrderData($request, $type);

        // Log request để debug
        Log::info('CheckoutController::processOrder', [
            'type'       => $type,
            'product_id' => $productId,
            'user_id'    => $user->id,
            'options'    => array_diff_key($options, array_flip(['password'])), // Không log password
        ]);

        // Gọi OrderService để xử lý toàn bộ quy trình
        $result = $this->orderService->placeOrder($type, $productId, $user->id, $options);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
                'html'    => '<script>toastr.error("' . addslashes($result['message']) . '", "Thông Báo");</script>',
            ]);
        }

        // Lấy credentials (nếu có từ options)
        $credentials = null;
        if (in_array($type, ['hosting', 'vps']) && isset($result['order']->options['username'])) {
            $credentials = [
                'username' => $result['order']->options['username'],
                'password' => $result['order']->options['password'],
                'ip'       => $result['order']->options['ip'] ?? 'Đang rà soát...',
            ];
        }

        // Tạo redirect URL theo loại sản phẩm
        $redirectUrl = $this->getSuccessRedirectUrl($type, $result['mgd']);

        return response()->json([
            'success'     => true,
            'message'     => $result['message'],
            'mgd'         => $result['mgd'],
            'redirect'    => $redirectUrl,
            'credentials' => $credentials, // Trả tài khoản về để hiện modal
            'html'        => '<script>toastr.success("' . addslashes($result['message']) . '", "Thông Báo");'
                        . ($redirectUrl ? 'setTimeout(function(){window.location.href="' . $redirectUrl . '";},1500);' : '')
                        . '</script>',
        ]);
    }

    // =========================================================================
    // BACKWARD COMPAT - Giữ lại các route cũ để không break frontend
    // =========================================================================

    /** @deprecated Dùng processOrder() thay thế */
    public function processDomain(Request $request)
    {
        return $this->processOrder($request, 'domain');
    }

    /** @deprecated Dùng processOrder() thay thế */
    public function processHosting(Request $request)
    {
        return $this->processOrder($request, 'hosting');
    }

    /** @deprecated Dùng processOrder() thay thế */
    public function processVPS(Request $request)
    {
        return $this->processOrder($request, 'vps');
    }

    /** @deprecated Dùng processOrder() thay thế */
    public function processSourceCode(Request $request)
    {
        return $this->processOrder($request, 'sourcecode');
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    /**
     * Trích xuất product_id và options từ request theo loại sản phẩm
     *
     * @return array [int $productId, array $options]
     */
    private function extractOrderData(Request $request, string $type): array
    {
        $voucher = $request->input('voucher');

        return match($type) {
            'domain' => [
                0, // Domain không dùng productId (dùng domain name trực tiếp)
                [
                    'domain'  => $request->input('domain', ''),
                    'ns1'     => $request->input('ns1', ''),
                    'ns2'     => $request->input('ns2', ''),
                    'hsd'     => $request->input('hsd', date('d/m/Y', strtotime('+1 year'))),
                    'voucher' => $voucher,
                ]
            ],
            'hosting' => [
                (int)$request->input('hosting_id', 0),
                [
                    'period'  => $request->input('period', 'month'),
                    'voucher' => $voucher,
                ]
            ],
            'vps' => [
                (int)$request->input('vps_id', 0),
                [
                    'period'  => $request->input('period', 'month'),
                    'voucher' => $voucher,
                ]
            ],
            'sourcecode' => [
                (int)$request->input('source_code_id', 0),
                [
                    'voucher' => $voucher,
                ]
            ],
            default => [0, []]
        };
    }

    /**
     * Lấy URL redirect sau khi mua thành công
     */
    private function getSuccessRedirectUrl(string $type, ?string $mgd): ?string
    {
        if (!$mgd) return null;

        return match($type) {
            'sourcecode' => route('download.index', ['mgd' => $mgd]),
            default      => null, // domain, hosting, vps không redirect ngay
        };
    }
}
