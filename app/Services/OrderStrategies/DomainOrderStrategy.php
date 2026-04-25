<?php

namespace App\Services\OrderStrategies;

use App\Contracts\OrderStrategyInterface;
use App\Models\Domain;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DomainOrderStrategy
 * Strategy xử lý logic mua tên miền (Domain)
 *
 * Thay thế logic bị copy-paste ở:
 * - CheckoutController::processDomain()
 * - AjaxController::buyDomain()
 * - DomainController::buy()
 * - DomainService::purchaseDomain()
 */
class DomainOrderStrategy implements OrderStrategyInterface
{
    public function getProductType(): string
    {
        return 'domain';
    }

    public function getProductTypeName(): string
    {
        return 'Tên Miền';
    }

    /**
     * Validate dữ liệu mua domain
     * Bắt buộc: domain (tên đầy đủ), ns1, ns2, hsd
     */
    public function validate(int $productId, array $options = []): bool|string
    {
        // productId không dùng cho domain (dùng tên domain trực tiếp)
        if (empty($options['domain'])) {
            return 'Vui lòng nhập tên miền!';
        }

        if (empty($options['ns1']) || empty($options['ns2'])) {
            return 'Vui lòng nhập đầy đủ Nameserver!';
        }

        if (empty($options['hsd'])) {
            return 'Vui lòng chọn thời hạn sử dụng!';
        }

        // Kiểm tra định dạng domain: phải có ít nhất 1 dấu chấm
        $parts = explode('.', $options['domain']);
        if (count($parts) < 2) {
            return 'Tên miền không hợp lệ!';
        }

        return true;
    }

    /**
     * Lấy giá domain theo đuôi miền
     * productId ở đây là ID của Domain type (bảng listdomain)
     */
    public function getPrice(int $productId, array $options = []): ?int
    {
        // Nếu có domain name trong options, tìm theo đuôi
        if (!empty($options['domain'])) {
            $parts = explode('.', $options['domain']);
            $extension = '.' . $parts[1];
            $domainType = Domain::where('duoi', ltrim($extension, '.'))->first();
            return $domainType ? (int)$domainType->price : null;
        }

        // Fallback: tìm theo ID
        $domainType = Domain::find($productId);
        return $domainType ? (int)$domainType->price : null;
    }

    /**
     * Tên sản phẩm là tên domain đầy đủ
     */
    public function getProductName(int $productId, array $options = []): string
    {
        return $options['domain'] ?? 'Domain #' . $productId;
    }

    /**
     * Kiểm tra domain chưa được mua (chưa tồn tại trong bảng history)
     */
    public function checkAvailability(int $productId, array $options = []): bool|string
    {
        $domainName = $options['domain'] ?? '';

        if (empty($domainName)) {
            return 'Tên miền không hợp lệ!';
        }

        // Kiểm tra đuôi miền có được hỗ trợ không
        $parts = explode('.', $domainName);
        $extension = ltrim('.' . $parts[1], '.');
        $domainType = Domain::where('duoi', $extension)->first();

        if (!$domainType) {
            return 'Đuôi miền .' . $extension . ' chưa được hỗ trợ!';
        }

        // Kiểm tra domain đã được mua chưa (trong hệ orders mới)
        $existing = Order::where('product_type', 'domain')
            ->where('options->domain', $domainName)
            ->first();
        if ($existing) {
            return 'Tên miền ' . $domainName . ' đã được mua trước đó!';
        }

        return true;
    }

    public function createOrder(int $userId, int $productId, string $mgd, array $options = [], ?int $price = null): Model
    {
        return Order::create([
            'user_id'      => $userId,
            'product_type' => 'domain',
            'product_id'   => 0, // Domain ko dùng ID sản phẩm, lưu thông tin vào options JSON
            'mgd'          => $mgd,
            'status'       => 0, // Chờ xử lý
            'price'        => $price ?? ($this->getPrice($productId, $options) ?? 0),
            'options'      => [
                'domain' => $options['domain'],
                'ns1'    => $options['ns1'],
                'ns2'    => $options['ns2'],
                'hsd'    => $options['hsd'],
                'ahihi'  => 0,
                'extension' => '.' . (explode('.', $options['domain'])[1] ?? '')
            ],
            'time'         => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Dữ liệu gửi Telegram khi có đơn domain mới
     */
    public function getTelegramData(int $userId, int $productId, string $mgd, array $options = []): array
    {
        $user = User::find($userId);
        return [
            'type'     => 'domain',
            'username' => $user?->taikhoan ?? 'Unknown',
            'mgd'      => $mgd,
            'domain'   => $options['domain'] ?? '',
            'ns1'      => $options['ns1'] ?? '',
            'ns2'      => $options['ns2'] ?? '',
            'time'     => date('d/m/Y - H:i:s'),
        ];
    }
}
