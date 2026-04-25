<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $time2   = date('d/m/Y');
        $time3   = date('m/Y');
        $homqua  = date('d/m/Y', strtotime('-1 day'));

        // Doanh thu từ thẻ cào
        $doanhthuhomnay = Card::sumAmountByStatusAndTime2(1, $time2);
        $doanhthuthang  = Card::sumAmountByStatusAndTime3(1, $time3);
        $doanhthuhqua   = Card::sumAmountByStatusAndTime2(1, $homqua);
        $tongdoanhthu   = Card::sumAmountByStatus(1);

        // Đơn hàng chờ xử lý (status=0) tổng cộng
        $donhang = Order::where('status', 0)->count();

        // Đơn hàng hoàn tất/duyệt (status=1) tổng cộng
        $donhoanthanh = Order::where('status', 1)->count();

        $thanhvien = User::count();

        // Domain yêu cầu cập nhật DNS (ahihi = 1)
        $update = Order::where('product_type', 'domain')
                       ->where('options->ahihi', 1)->count();

        // Phân bổ đơn hàng theo loại sản phẩm (dùng cho biểu đồ)
        $orderByType = [
            'Domain'      => Order::where('product_type', 'domain')->count(),
            'Hosting'     => Order::where('product_type', 'hosting')->count(),
            'VPS'         => Order::where('product_type', 'vps')->count(),
            'Source Code' => Order::where('product_type', 'sourcecode')->count(),
        ];

        // Đơn hàng theo trạng thái
        $orderByStatus = [
            'Chờ Xử Lý' => Order::where('status', 0)->count(),
            'Đã Duyệt'   => Order::where('status', 1)->count(),
            'Từ Chối'    => Order::where('status', 2)->count(),
        ];

        return view('admin.dashboard', compact(
            'doanhthuhomnay', 'doanhthuthang', 'doanhthuhqua', 'tongdoanhthu',
            'donhang', 'donhoanthanh', 'thanhvien', 'update',
            'orderByType', 'orderByStatus'
        ));
    }
}
