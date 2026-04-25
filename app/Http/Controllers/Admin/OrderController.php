<?php
// Khai báo namespace cho Controller này - thuộc App\Http\Controllers\Admin
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Class OrderController
 * Controller xử lý quản lý đơn hàng trong admin panel
 */
class OrderController extends Controller
{
    /**
     * Hiển thị danh sách tất cả đơn hàng với bộ lọc
     * Cho phép lọc theo loại đơn hàng (domain, hosting, VPS, source code) và trạng thái
     * 
     * @param Request $request - HTTP request chứa type và status từ query
     * @return \Illuminate\View\View - View danh sách đơn hàng
     */
    public function index(Request $request)
    {
        $type = $request->get('type', 'all');
        $status = $request->get('status', 'all');
        
        $query = Order::with('user')->orderBy('id', 'desc');

        if ($type !== 'all') {
            $query->where('product_type', $type);
        }
        
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        // Cần map thêm các tuỳ biến vào Array vì toArray() mặc định sẽ không gọi Magic Accessors
        $orders = $query->get()->map(function($order) {
            $arr = $order->toArray(); // Chứa thông tin gốc và user (do eager load)
            $arr['order_type'] = $order->product_type;
            
            // Lôi Data từ cột JSON ra map cứng vào Top-Level Array cho Blade sử dụng
            $arr['domain'] = $order->domain;
            $arr['ns1'] = $order->ns1;
            $arr['ns2'] = $order->ns2;
            $arr['period'] = $order->period;
            
            // Xử lý các loại hàng có ID trỏ sang bảng khác
            if (in_array($order->product_type, ['hosting', 'vps', 'sourcecode'])) {
                $productKey = $order->product_type == 'sourcecode' ? 'source_code' : $order->product_type;
                $product = $order->product();
                $arr[$productKey] = $product ? $product->toArray() : [];
            }
            
            return $arr;
        })->toArray();
        
        return view('admin.orders.index', compact('orders', 'type', 'status'));
    }

    /**
     * Hiển thị chi tiết đơn hàng cụ thể
     * 
     * @param int $id - ID đơn hàng
     * @param string $type - Loại đơn hàng: 'domain', 'hosting', 'vps', 'sourcecode'
     * @return \Illuminate\View\View - View chi tiết đơn hàng
     */
    public function show($id, $type)
    {
        $order = Order::with('user')->findOrFail($id);
        $order->order_type = $order->product_type; // Tương thích view cũ
        
        // Bơm biến quan trệ sản phẩm phụ thuộc để view hiển thị thông số như $order->hosting->name
        if (in_array($order->product_type, ['hosting', 'vps', 'sourcecode'])) {
            $productKey = $order->product_type == 'sourcecode' ? 'sourceCode' : $order->product_type;
            $order->{$productKey} = $order->product();
        }

        return view('admin.orders.show', compact('order', 'type'));
    }

    /**
     * Duyệt đơn hàng đang chờ
     * Cập nhật trạng thái đơn hàng thành 1 (Đã duyệt/Hoạt động)
     * 
     * @param int $id - ID đơn hàng
     * @param string $type - Loại đơn hàng: 'domain', 'hosting', 'vps', 'sourcecode'
     * @return \Illuminate\Http\RedirectResponse - Redirect về danh sách đơn hàng với thông báo
     */
    public function approve($id, $type)
    {
        try {
            $order = Order::findOrFail($id);
            $order->status = 1; // 1 = Đã duyệt
            $order->save();
            
            return redirect()->route('admin.orders.index')->with('success', 'Đơn hàng đã được duyệt thành công');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Từ chối đơn hàng và hoàn tiền cho user
     * Cập nhật trạng thái đơn hàng thành 4 (Từ chối) và tính toán số tiền hoàn lại
     * 
     * @param Request $request - HTTP request (không sử dụng trong code này)
     * @param int $id - ID đơn hàng
     * @param string $type - Loại đơn hàng: 'domain', 'hosting', 'vps', 'sourcecode'
     * @return \Illuminate\Http\RedirectResponse - Redirect về danh sách đơn hàng với thông báo
     */
    public function reject(Request $request, $id, $type)
    {
        try {
            $order = Order::with('user')->findOrFail($id);
            $order->status = 4; // Từ chối
            $order->save();
            
            $refundAmount = $order->price;
            
            // Tương thích ngược: Nếu đơn hàng cũ (price=0), lấy giá từ sản phẩm gốc
            if ($refundAmount == 0) {
                try {
                    // OOP: Uỷ quyền cho Factory và Strategy để tính giá (tương thích mọi class sau này)
                    $strategy = \App\Services\OrderStrategyFactory::make($order->product_type);
                    $refundAmount = $strategy->getPrice((int)$order->product_id, $order->options ?? []) ?? 0;
                } catch (\Exception $e) {
                    $refundAmount = 0; // Fallback an toàn nếu lỗi logic
                }
            }
            
            // Hoàn tiền cho user
            if ($refundAmount > 0 && $order->user) {
                $order->user->incrementBalance($refundAmount);
            }
            
            return redirect()->route('admin.orders.index')->with('success', 'Đơn hàng đã bị từ chối và hoàn tiền thành công');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
