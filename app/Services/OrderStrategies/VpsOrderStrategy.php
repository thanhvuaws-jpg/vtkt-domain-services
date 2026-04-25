<?php

namespace App\Services\OrderStrategies;

use App\Contracts\OrderStrategyInterface;
use App\Models\VPS;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Class VpsOrderStrategy
 * Strategy xử lý logic mua VPS
 *
 * Thay thế logic bị copy-paste ở:
 * - CheckoutController::processVPS()
 * - AjaxController::buyVPS()
 */
class VpsOrderStrategy implements OrderStrategyInterface
{
    public function getProductType(): string
    {
        return 'vps';
    }

    public function getProductTypeName(): string
    {
        return 'VPS';
    }

    /**
     * Validate dữ liệu mua VPS
     * Bắt buộc: vps_id, period (month/year)
     */
    public function validate(int $productId, array $options = []): bool|string
    {
        if ($productId <= 0) {
            return 'Vui lòng chọn gói VPS!';
        }

        $period = $options['period'] ?? '';
        if (!in_array($period, ['month', 'year'])) {
            return 'Kỳ hạn không hợp lệ! Vui lòng chọn theo tháng hoặc theo năm.';
        }

        return true;
    }

    /**
     * Lấy giá VPS theo gói và kỳ hạn
     */
    public function getPrice(int $productId, array $options = []): ?int
    {
        $vps = VPS::find($productId);
        if (!$vps) {
            return null;
        }

        $period = $options['period'] ?? 'month';

        return match($period) {
            'month' => (int)$vps->price_month,
            'year'  => (int)$vps->price_year,
            default => (int)$vps->price_month,
        };
    }

    /**
     * Tên sản phẩm = tên gói VPS
     */
    public function getProductName(int $productId, array $options = []): string
    {
        $vps = VPS::find($productId);
        return $vps?->name ?? 'VPS #' . $productId;
    }

    /**
     * VPS luôn khả dụng (không giới hạn số lượng)
     */
    public function checkAvailability(int $productId, array $options = []): bool|string
    {
        $vps = VPS::find($productId);
        if (!$vps) {
            return 'Không tìm thấy gói VPS này!';
        }

        return true;
    }

    public function createOrder(int $userId, int $productId, string $mgd, array $options = [], ?int $price = null): Model
    {
        // Auto-generate credentials for immediate provisioning UI
        $username = 'vps_' . \Illuminate\Support\Str::random(5);
        $password = \Illuminate\Support\Str::random(10);
        $ip       = '103.' . rand(10, 255) . '.' . rand(10, 255) . '.' . rand(10, 255); // Random IP placeholder

        return Order::create([
            'user_id'      => $userId,
            'product_type' => 'vps',
            'product_id'   => $productId,
            'status'       => 0, // Chờ xử lý
            'mgd'          => $mgd,
            'price'        => $price ?? ($this->getPrice($productId, $options) ?? 0),
            'options'      => [
                'period'   => $options['period'] ?? 'month',
                'username' => strtolower($username),
                'password' => $password,
                'ip'       => $ip,
            ],
            'time'         => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Dữ liệu gửi Telegram khi có đơn VPS mới
     */
    public function getTelegramData(int $userId, int $productId, string $mgd, array $options = []): array
    {
        $user = User::find($userId);
        $vps = VPS::find($productId);
        $period = ($options['period'] ?? 'month') === 'year' ? '1 năm' : '1 tháng';

        return [
            'type'         => 'vps',
            'username'     => $user?->taikhoan ?? 'Unknown',
            'mgd'          => $mgd,
            'product_name' => $vps?->name ?? 'VPS #' . $productId,
            'period'       => $period,
            'time'         => date('d/m/Y - H:i:s'),
        ];
    }
}
