<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Model;

/**
 * Interface OrderStrategyInterface
 * Định nghĩa giao diện chung cho tất cả các Strategy mua hàng
 *
 * Mỗi loại sản phẩm (Domain, Hosting, VPS, SourceCode) sẽ implement interface này.
 * Khi thêm sản phẩm mới, chỉ cần tạo class mới implement interface này
 * mà không cần sửa code ở CheckoutController, AjaxController, hay OrderService.
 */
interface OrderStrategyInterface
{
    /**
     * Trả về loại sản phẩm (product type identifier)
     * Dùng để routing và display
     *
     * @return string Ví dụ: 'domain', 'hosting', 'vps', 'sourcecode'
     */
    public function getProductType(): string;

    /**
     * Trả về tên hiển thị của loại sản phẩm
     *
     * @return string Ví dụ: 'Tên Miền', 'Hosting', 'VPS', 'Source Code'
     */
    public function getProductTypeName(): string;

    /**
     * Validate dữ liệu đầu vào trước khi mua
     * Kiểm tra các trường bắt buộc theo từng loại sản phẩm
     *
     * @param int   $productId ID của sản phẩm cần mua
     * @param array $options   Dữ liệu bổ sung (ns1/ns2 cho domain, period cho hosting/VPS)
     * @return true|string true nếu hợp lệ, string error message nếu không hợp lệ
     */
    public function validate(int $productId, array $options = []): bool|string;

    /**
     * Lấy giá của sản phẩm
     * Với Hosting/VPS: giá có thể khác nhau theo period (month/year)
     *
     * @param int   $productId ID sản phẩm
     * @param array $options   Dữ liệu bổ sung (period, etc.)
     * @return int|null Giá (VND) hoặc null nếu không tìm thấy sản phẩm
     */
    public function getPrice(int $productId, array $options = []): ?int;

    /**
     * Lấy tên sản phẩm để hiển thị trong thông báo
     *
     * @param int   $productId ID sản phẩm
     * @param array $options   Dữ liệu bổ sung
     * @return string Tên sản phẩm (ví dụ: "example.com", "Hosting Cơ Bản")
     */
    public function getProductName(int $productId, array $options = []): string;

    /**
     * Tạo đơn hàng trong database
     * Mỗi loại sản phẩm sẽ ghi vào bảng history tương ứng của nó
     *
     * @param int    $userId    ID người dùng mua hàng
     * @param int    $productId ID sản phẩm
     * @param string $mgd       Mã giao dịch duy nhất
     * @param array  $options   Dữ liệu bổ sung (ns1/ns2 cho domain, period cho hosting/VPS)
     * @param int|null $price   Giá thực tế sau khi áp dụng voucher (nếu có)
     * @return Model Instance của đơn hàng vừa tạo (History, HostingHistory, VPSHistory, SourceCodeHistory)
     */
    public function createOrder(int $userId, int $productId, string $mgd, array $options = [], ?int $price = null): Model;

    /**
     * Kiểm tra sản phẩm có tồn tại và còn khả dụng để mua không
     * Ví dụ: Domain kiểm tra chưa được mua, Hosting kiểm tra còn slot
     *
     * @param int   $productId ID sản phẩm
     * @param array $options   Dữ liệu bổ sung
     * @return true|string true nếu khả dụng, string error message nếu không
     */
    public function checkAvailability(int $productId, array $options = []): bool|string;

    /**
     * Lấy dữ liệu để gửi thông báo Telegram khi có đơn hàng mới
     *
     * @param int    $userId    ID người dùng
     * @param int    $productId ID sản phẩm
     * @param string $mgd       Mã giao dịch
     * @param array  $options   Dữ liệu bổ sung
     * @return array Mảng dữ liệu cho TelegramService::notifyNewOrder()
     */
    public function getTelegramData(int $userId, int $productId, string $mgd, array $options = []): array;
}
