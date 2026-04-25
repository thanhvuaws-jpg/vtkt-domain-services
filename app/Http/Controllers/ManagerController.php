<?php
// Khai báo namespace cho Controller này - thuộc App\Http\Controllers
namespace App\Http\Controllers;

// Import các Model cần thiết
use App\Models\Order;
use App\Models\User; // Model quản lý người dùng
use Illuminate\Http\Request; // Class xử lý HTTP request

/**
 * Class ManagerController
 * Controller xử lý trang quản lý tất cả dịch vụ đã mua của user
 */
class ManagerController extends Controller
{
    /**
     * Hiển thị tất cả dịch vụ đã mua của user
     * Bao gồm: domain, hosting, VPS, source code
     * 
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        // Kiểm tra user đã đăng nhập chưa
        if (!session()->has('users')) {
            return redirect()->route('login');
        }

        // Lấy username từ session
        $username = session('users');
        // Tìm user trong database theo username
        $user = User::where('taikhoan', $username)->first();

        // Nếu không tìm thấy user, redirect đến trang đăng nhập với thông báo lỗi
        if (!$user) {
            return redirect()->route('login')->with('error', 'Không tìm thấy thông tin người dùng!');
        }

        // Lấy đơn hàng domain
        $domainOrders = Order::where('user_id', $user->id)->where('product_type', 'domain')
            ->orderBy('id', 'desc')->get();

        // Lấy đơn hàng hosting (kèm thông tin gói hosting)
        $hostingOrders = Order::where('user_id', $user->id)->where('product_type', 'hosting')
            ->orderBy('id', 'desc')->get()->map(function($o) {
                $o->hosting = $o->product();
                return $o;
            });

        // Lấy đơn hàng VPS (kèm thông tin VPS)
        $vpsOrders = Order::where('user_id', $user->id)->where('product_type', 'vps')
            ->orderBy('id', 'desc')->get()->map(function($o) {
                $o->vps = $o->product();
                return $o;
            });

        // Lấy đơn hàng Source code (kèm thông tin)
        $sourceCodeOrders = Order::where('user_id', $user->id)->where('product_type', 'sourcecode')
            ->orderBy('id', 'desc')->get()->map(function($o) {
                $o->sourceCode = $o->product();
                return $o;
            });

        // Trả về view với tất cả dữ liệu đơn hàng
        return view('pages.manager', compact(
            'user', // Thông tin user
            'domainOrders', // Đơn hàng domain
            'hostingOrders', // Đơn hàng hosting
            'vpsOrders', // Đơn hàng VPS
            'sourceCodeOrders' // Đơn hàng source code
        ));
    }
}
