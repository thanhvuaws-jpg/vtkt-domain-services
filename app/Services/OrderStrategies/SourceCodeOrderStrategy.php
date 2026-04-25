<?php

namespace App\Services\OrderStrategies;

use App\Contracts\OrderStrategyInterface;
use App\Models\SourceCode;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SourceCodeOrderStrategy
 * Strategy xử lý logic mua Source Code
 *
 * Thay thế logic bị copy-paste ở:
 * - CheckoutController::processSourceCode()
 * - AjaxController::buySourceCode()
 *
 * Khác biệt so với Domain/Hosting/VPS:
 * - Không có kỳ hạn (period)
 * - Đơn hàng duyệt ngay (status = 1) thay vì chờ
 * - Sau khi mua có thể download ngay
 */
class SourceCodeOrderStrategy implements OrderStrategyInterface
{
    public function getProductType(): string
    {
        return 'sourcecode';
    }

    public function getProductTypeName(): string
    {
        return 'Source Code';
    }

    /**
     * Validate dữ liệu mua source code
     * Chỉ cần source_code_id hợp lệ
     */
    public function validate(int $productId, array $options = []): bool|string
    {
        if ($productId <= 0) {
            return 'Vui lòng chọn source code!';
        }

        return true;
    }

    /**
     * Lấy giá source code (giá cố định, không theo kỳ)
     */
    public function getPrice(int $productId, array $options = []): ?int
    {
        $sourceCode = SourceCode::find($productId);
        if (!$sourceCode) {
            return null;
        }

        return (int)$sourceCode->price;
    }

    /**
     * Tên sản phẩm = tên source code
     */
    public function getProductName(int $productId, array $options = []): string
    {
        $sourceCode = SourceCode::find($productId);
        return $sourceCode?->name ?? 'Source Code #' . $productId;
    }

    /**
     * Kiểm tra source code tồn tại
     * (Không giới hạn số lượng mua - mỗi user có thể mua 1 lần)
     */
    public function checkAvailability(int $productId, array $options = []): bool|string
    {
        $sourceCode = SourceCode::find($productId);
        if (!$sourceCode) {
            return 'Không tìm thấy source code này!';
        }

        return true;
    }

    public function createOrder(int $userId, int $productId, string $mgd, array $options = [], ?int $price = null): Model
    {
        return Order::create([
            'user_id'      => $userId,
            'product_type' => 'sourcecode',
            'product_id'   => $productId,
            'status'       => 1, // Duyệt ngay (source code download ngay)
            'mgd'          => $mgd,
            'price'        => $price ?? ($this->getPrice($productId, $options) ?? 0),
            'options'      => [],
            'time'         => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Dữ liệu gửi Telegram khi có đơn source code mới
     */
    public function getTelegramData(int $userId, int $productId, string $mgd, array $options = []): array
    {
        $user = User::find($userId);
        $sourceCode = SourceCode::find($productId);

        return [
            'type'         => 'sourcecode',
            'username'     => $user?->taikhoan ?? 'Unknown',
            'mgd'          => $mgd,
            'product_name' => $sourceCode?->name ?? 'Source Code #' . $productId,
            'time'         => date('d/m/Y - H:i:s'),
        ];
    }
}
