<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

/**
 * Class DomainController
 * Controller xử lý: checkout domain, mua domain, quản lý DNS
 *
 * REFACTORED: Xóa private generateMGD() và buy() duplicate
 * → buy() giờ là wrapper gọi OrderService::placeOrder('domain')
 */
class DomainController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService  = $orderService;
    }

    /**
     * Trang checkout domain
     */
    public function checkout(Request $request)
    {
        $domain = $request->get('domain', '');

        if (empty($domain)) {
            return redirect()->route('home')->with('error', 'Vui lòng chọn tên miền');
        }

        $explode   = explode(".", $domain);
        $duoimien  = isset($explode[1]) ? '.' . $explode[1] : '';
        $domainInfo = Domain::where('duoi', $duoimien)->first();

        if (!$domainInfo || $domainInfo->duoi != $duoimien) {
            return redirect()->route('home')->with('error', 'Đuôi miền không hợp lệ');
        }

        return view('pages.checkout.domain', [
            'domainName' => $domain,
            'domain'     => $domainInfo,
            'price'      => $domainInfo->price
        ]);
    }

    /**
     * Mua domain (AJAX) - Wrapper gọi OrderService
     */
    public function buy(Request $request)
    {
        if (!Session::has('users')) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để thực hiện!',
                'html'    => '<script>toastr.error("Vui Lòng Đăng Nhập Để Thực Hiện!", "Thông Báo");</script>'
            ]);
        }

        $user = User::findByUsername(Session::get('users'));
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin tài khoản!',
                'html'    => '<script>toastr.error("Không tìm thấy thông tin tài khoản!", "Thông Báo");</script>'
            ]);
        }

        $result = $this->orderService->placeOrder('domain', 0, $user->id, [
            'domain'  => $request->input('domain', ''),
            'ns1'     => $request->input('ns1', ''),
            'ns2'     => $request->input('ns2', ''),
            'hsd'     => $request->input('hsd', '1'),
            'voucher' => $request->input('voucher'),
        ]);
        
        // Thêm log để bắt voucher
        if ($request->filled('voucher')) {
            \Log::info("Domain Purchase with Voucher Attempt: " . $request->input('voucher'));
        }

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'html'    => $result['success']
                ? '<script>toastr.success("' . addslashes($result['message']) . '", "Thông Báo");</script>'
                : '<script>toastr.error("' . addslashes($result['message']) . '", "Thông Báo");</script>',
        ]);
    }

    /**
     * Trang quản lý domain
     */
    public function manageDomain($id)
    {
        if (!Session::has('users')) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập');
        }

        $user = User::where('taikhoan', Session::get('users'))->first();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Không tìm thấy thông tin người dùng');
        }

        $domainHistory = \App\Models\Order::where('id', $id)
            ->where('user_id', $user->id)
            ->where('product_type', 'domain')
            ->first();

        if (!$domainHistory) {
            return redirect()->route('manager.index')->with('error', 'Bạn không có quyền quản lý miền này!');
        }

        if ($domainHistory->status == 4) {
            return redirect()->route('manager.index')->with('error', 'Tên miền này đã bị từ chối hỗ trợ!');
        }

        return view('pages.manage-domain', ['domainHistory' => $domainHistory]);
    }

    /**
     * Cập nhật DNS domain (chu kỳ 15 ngày)
     */
    public function updateDns(Request $request, $id = null)
    {
        if (!Session::has('users')) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập',
                'html'    => '<script>toastr.error("Vui Lòng Đăng Nhập!", "Thông Báo");</script>'
            ]);
        }

        $request->validate(['ns1' => 'required|string', 'ns2' => 'required|string']);

        $mgd  = $request->mgd ?? null;
        $user = User::where('taikhoan', Session::get('users'))->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin người dùng',
                'html'    => '<script>toastr.error("Không Tìm Thấy Thông Tin Người Dùng!", "Thông Báo");</script>'
            ]);
        }

        if ($id) {
            $domainHistory = \App\Models\Order::where('id', $id)->where('user_id', $user->id)->first();
        } elseif ($mgd) {
            $domainHistory = \App\Models\Order::where('mgd', $mgd)->where('user_id', $user->id)->first();
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Thiếu thông tin domain',
                'html'    => '<script>toastr.error("Thiếu Thông Tin Domain!", "Thông Báo");</script>'
            ]);
        }

        if (!$domainHistory) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền quản lý miền này!',
                'html'    => '<script>toastr.error("Bạn Không Có Quyền Quản Lý Miền Này!", "Thông Báo");</script>'
            ]);
        }

        if ($domainHistory->timedns != '0') {
            try {
                $lastUpdateDate = \Carbon\Carbon::createFromFormat('d/m/Y', $domainHistory->timedns);
                $today          = \Carbon\Carbon::now();
                $daysDiff       = $today->diffInDays($lastUpdateDate, false);

                if ($daysDiff < 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Dữ liệu ngày cập nhật không hợp lệ. Vui lòng liên hệ admin!',
                        'html'    => '<script>toastr.error("Dữ Liệu Ngày Cập Nhật Không Hợp Lệ!", "Thông Báo");</script>'
                    ]);
                }

                if ($daysDiff < 15) {
                    $daysRemaining = 15 - $daysDiff;
                    return response()->json([
                        'success' => false,
                        'message' => "Bạn chỉ có thể thay đổi DNS sau 15 ngày! Còn {$daysRemaining} ngày nữa.",
                        'html'    => "<script>toastr.error('Còn {$daysRemaining} Ngày Nữa Mới Được Cập Nhật DNS.', 'Thông Báo');</script>"
                    ]);
                }
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lỗi xử lý ngày tháng. Vui lòng liên hệ admin!',
                    'html'    => '<script>toastr.error("Lỗi Xử Lý Ngày Tháng!", "Thông Báo");</script>'
                ]);
            }
        }

        $domainHistory->ns1     = $request->ns1;
        $domainHistory->ns2     = $request->ns2;
        $domainHistory->ahihi   = 1;
        $domainHistory->status  = 3; // Chờ duyệt
        $domainHistory->timedns = date('d/m/Y');
        $domainHistory->save();

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật DNS thành công, chờ admin duyệt!',
            'html'    => '<script>toastr.success("Cập Nhật DNS Thành Công, Chờ Admin Duyệt!", "Thông Báo");</script>'
        ]);
    }
}
