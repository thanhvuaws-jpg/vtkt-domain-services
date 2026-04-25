<?php

namespace App\Helpers;

use App\Models\Order;

/**

 * Class OrderHelper
 * Helper dùng chung cho các thao tác liên quan đến đơn hàng
 */
class OrderHelper
{
    /**
     * Tạo mã giao dịch (MGD) duy nhất trên toàn hệ thống
     *
     * @return string Mã giao dịch dạng chuỗi (MGD + timestamp + số ngẫu nhiên)
     */
    public static function generateMGD(): string
    {
        do {
            // Tạo mã = MGD + timestamp + số ngẫu nhiên 4 chữ số
            $mgd = 'MGD' . time() . rand(1000, 9999);
        } while (
            // Đảm bảo MGD chưa tồn tại trong bảng orders
            Order::where('mgd', $mgd)->exists()
        );

        return $mgd;
    }

    /**
     * Lấy nhãn trạng thái đơn hàng dạng chuỗi
     *
     * @param int $status Trạng thái (0=Chờ, 1=Duyệt, 2=Hủy, 3=Hoàn thành, 4=Từ chối)
     * @return string Nhãn trạng thái tiếng Việt
     */
    public static function getStatusLabel(int $status): string
    {
        return match($status) {
            0 => 'Chờ Xử Lý',
            1 => 'Đã Duyệt',
            2 => 'Đã Hủy',
            3 => 'Hoàn Thành',
            4 => 'Từ Chối',
            default => 'Không Xác Định',
        };
    }

    /**
     * Lấy class CSS badge tương ứng với trạng thái
     *
     * @param int $status Trạng thái đơn hàng
     * @return string CSS class cho badge
     */
    public static function getStatusBadgeClass(int $status): string
    {
        return match($status) {
            0 => 'warning',
            1 => 'success',
            2 => 'secondary',
            3 => 'info',
            4 => 'danger',
            default => 'secondary',
        };
    }
}
