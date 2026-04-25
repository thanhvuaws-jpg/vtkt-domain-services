<?php

namespace App\Services\OrderStrategies;

use App\Contracts\OrderStrategyInterface;
use App\Models\Hosting;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Class HostingOrderStrategy
 * Strategy xử lý logic mua Hosting
 *
 * Thay thế logic bị copy-paste ở:
 * - CheckoutController::processHosting()
 * - AjaxController::buyHosting()
 */
class HostingOrderStrategy implements OrderStrategyInterface
{
    public function getProductType(): string
    {
        return 'hosting';
    }

    public function getProductTypeName(): string
    {
        return 'Hosting';
    }

    /**
     * Validate dữ liệu mua hosting
     * Bắt buộc: hosting_id, period (month/year)
     */
    public function validate(int $productId, array $options = []): bool|string
    {
        if ($productId <= 0) {
            return 'Vui lòng chọn gói hosting!';
        }

        $period = $options['period'] ?? '';
        if (!in_array($period, ['month', 'year'])) {
            return 'Kỳ hạn không hợp lệ! Vui lòng chọn theo tháng hoặc theo năm.';
        }

        return true;
    }

    /**
     * Lấy giá hosting theo gói và kỳ hạn
     * Kỳ tháng = price_month, kỳ năm = price_year
     */
    public function getPrice(int $productId, array $options = []): ?int
    {
        $hosting = Hosting::find($productId);
        if (!$hosting) {
            return null;
        }

        $period = $options['period'] ?? 'month';

        return match($period) {
            'month' => (int)$hosting->price_month,
            'year'  => (int)$hosting->price_year,
            default => (int)$hosting->price_month,
        };
    }

    /**
     * Tên sản phẩm = tên gói hosting
     */
    public function getProductName(int $productId, array $options = []): string
    {
        $hosting = Hosting::find($productId);
        return $hosting?->name ?? 'Hosting #' . $productId;
    }

    /**
     * Hosting luôn khả dụng (không giới hạn số lượng)
     */
    public function checkAvailability(int $productId, array $options = []): bool|string
    {
        $hosting = Hosting::find($productId);
        if (!$hosting) {
            return 'Không tìm thấy gói hosting này!';
        }

        return true;
    }

    public function createOrder(int $userId, int $productId, string $mgd, array $options = [], ?int $price = null): Model
    {
        // Auto-generate credentials for immediate provisioning UI
        $username = 'host_' . \Illuminate\Support\Str::random(5);
        $password = \Illuminate\Support\Str::random(10);
        $ip       = '103.' . rand(10, 255) . '.' . rand(10, 255) . '.' . rand(10, 255); // Random IP placeholder

        return Order::create([
            'user_id'      => $userId,
            'product_type' => 'hosting',
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
     * Dữ liệu gửi Telegram khi có đơn hosting mới
     */
    public function getTelegramData(int $userId, int $productId, string $mgd, array $options = []): array
    {
        $user = User::find($userId);
        $hosting = Hosting::find($productId);
        $period = ($options['period'] ?? 'month') === 'year' ? '1 năm' : '1 tháng';

        return [
            'type'         => 'hosting',
            'username'     => $user?->taikhoan ?? 'Unknown',
            'mgd'          => $mgd,
            'product_name' => $hosting?->name ?? 'Hosting #' . $productId,
            'period'       => $period,
            'time'         => date('d/m/Y - H:i:s'),
        ];
    }
}
