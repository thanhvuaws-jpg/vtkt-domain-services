<?php

namespace App\Services;

use App\Contracts\OrderStrategyInterface;
use App\Services\OrderStrategies\DomainOrderStrategy;
use App\Services\OrderStrategies\HostingOrderStrategy;
use App\Services\OrderStrategies\VpsOrderStrategy;
use App\Services\OrderStrategies\SourceCodeOrderStrategy;
use InvalidArgumentException;

/**
 * Class OrderStrategyFactory
 * Factory tạo ra các Strategy instances theo loại sản phẩm
 *
 * Đây là điểm mở rộng duy nhất khi thêm sản phẩm mới:
 * 1. Tạo class mới implement OrderStrategyInterface
 * 2. Đăng ký trong mảng $strategies bên dưới
 * → Không cần sửa thêm file nào khác!
 */
class OrderStrategyFactory
{
    /**
     * Bản đồ product_type → Strategy class
     * Khi thêm sản phẩm mới, chỉ cần thêm 1 dòng vào đây
     */
    private static array $strategies = [
        'domain'     => DomainOrderStrategy::class,
        'hosting'    => HostingOrderStrategy::class,
        'vps'        => VpsOrderStrategy::class,
        'sourcecode' => SourceCodeOrderStrategy::class,
    ];

    /**
     * Tạo Strategy instance cho loại sản phẩm được chỉ định
     *
     * @param string $productType Loại sản phẩm ('domain', 'hosting', 'vps', 'sourcecode')
     * @return OrderStrategyInterface Strategy instance tương ứng
     * @throws InvalidArgumentException Nếu product type không được hỗ trợ
     */
    public static function make(string $productType): OrderStrategyInterface
    {
        $strategyClass = self::$strategies[$productType] ?? null;

        if (!$strategyClass) {
            throw new InvalidArgumentException(
                "Loại sản phẩm '{$productType}' không được hỗ trợ. " .
                "Các loại hợp lệ: " . implode(', ', self::getSupportedTypes())
            );
        }

        return new $strategyClass();
    }

    /**
     * Trả về danh sách tất cả loại sản phẩm được hỗ trợ
     * Dùng để:
     * - Validate input trong controllers
     * - Hiển thị dropdown trong admin
     * - Thay thế hardcoded switch/case trong Admin/OrderController
     *
     * @return array<string> Danh sách product types
     */
    public static function getSupportedTypes(): array
    {
        return array_keys(self::$strategies);
    }

    /**
     * Trả về danh sách loại sản phẩm kèm tên hiển thị
     * Dùng để render dropdown select
     *
     * @return array<string, string> ['domain' => 'Tên Miền', ...]
     */
    public static function getSupportedTypesWithLabels(): array
    {
        $result = [];
        foreach (self::$strategies as $type => $class) {
            $strategy = new $class();
            $result[$type] = $strategy->getProductTypeName();
        }
        return $result;
    }

    /**
     * Kiểm tra xem product type có được hỗ trợ không
     *
     * @param string $productType
     * @return bool
     */
    public static function isSupported(string $productType): bool
    {
        return isset(self::$strategies[$productType]);
    }
}
