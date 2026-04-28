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

        // Tách logic thành một hàm xử lý chung để tái sử dụng (Chuẩn Clean Code DRY)
        $hostingOrders = $this->loadOrderProducts($user->id, 'hosting', \App\Models\Hosting::class, 'hosting');
        $vpsOrders = $this->loadOrderProducts($user->id, 'vps', \App\Models\VPS::class, 'vps');
        $sourceCodeOrders = $this->loadOrderProducts($user->id, 'sourcecode', \App\Models\SourceCode::class, 'sourceCode');

        // Trả về view với tất cả dữ liệu đơn hàng
        return view('pages.manager', compact(
            'user', // Thông tin user
            'domainOrders', // Đơn hàng domain
            'hostingOrders', // Đơn hàng hosting
            'vpsOrders', // Đơn hàng VPS
            'sourceCodeOrders' // Đơn hàng source code
        ));
    }

    /**
     * Hàm hỗ trợ tải danh sách sản phẩm theo Order và gán vào thuộc tính (Fix N+1 Query).
     * 
     * @param int $userId ID tài khoản người dùng
     * @param string $productType Loại sản phẩm (hosting, vps, sourcecode)
     * @param string $modelClass Tên class Model (VD: \App\Models\Hosting::class)
     * @param string $relationName Tên thuộc tính sẽ gán vào Object (VD: 'hosting')
     * @return \Illuminate\Support\Collection
     */
    private function loadOrderProducts(int $userId, string $productType, string $modelClass, string $relationName)
    {
        $orders = Order::where('user_id', $userId)
            ->where('product_type', $productType)
            ->orderBy('id', 'desc')
            ->get();

        $productIds = $orders->pluck('product_id')->unique()->toArray();
        $products = $modelClass::whereIn('id', $productIds)->get()->keyBy('id');

        return $orders->map(function($o) use ($products, $relationName) {
            $o->$relationName = $products[$o->product_id] ?? null;
            return $o;
        });
    }
}
