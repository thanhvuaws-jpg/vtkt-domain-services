<?php
// Khai báo namespace cho Controller này - thuộc App\Http\Controllers\Admin
namespace App\Http\Controllers\Admin;

// Import Controller base class
use App\Http\Controllers\Controller;
// Import các Model cần thiết
use App\Models\User; // Model quản lý người dùng
use Illuminate\Http\Request; // Class xử lý HTTP request

/**
 * Class WalletController
 * Controller xử lý quản lý nạp ví (cộng tiền thủ công) trong admin panel
 */
class WalletController extends Controller
{
    /**
     * Hiển thị form cộng tiền thủ công cho user
     * 
     * @return \Illuminate\View\View - View form cộng tiền
     */
    public function index()
    {
        // Trả về view form cộng tiền
        return view('admin.wallet.index');
    }

    /**
     * Cộng tiền thủ công cho user
     * Admin có thể cộng tiền trực tiếp cho user mà không cần qua thẻ cào
     * 
     * @param Request $request - HTTP request chứa idc (user ID) và price (số tiền)
     * @return \Illuminate\Http\RedirectResponse - Redirect về form cộng tiền với thông báo
     */
    public function addBalance(Request $request)
    {
        // Validate dữ liệu đầu vào từ form
        $request->validate([
            'idc' => 'required|integer', // ID user bắt buộc, phải là số nguyên
            'price' => 'required|integer|min:0', // Số tiền bắt buộc, phải là số nguyên >= 0
        ]);

        // Tìm user theo ID
        $user = User::find($request->idc);
        
        // Nếu không tìm thấy user, redirect với thông báo lỗi
        if (!$user) {
            return redirect()->route('admin.wallet.index')
                ->with('error', 'Không tìm thấy người dùng với ID ' . $request->idc);
        }

        // Cộng tiền cho user bằng phương thức incrementBalance của User model
        $user->incrementBalance((int)$request->price);

        // Redirect về form cộng tiền với thông báo thành công
        return redirect()->route('admin.wallet.index')
            ->with('success', 'Giao dịch cộng ' . number_format($request->price) . 'đ thành công cho người dùng ' . $user->taikhoan);
    }
}
