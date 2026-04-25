<?php

namespace App\Contracts;

/**
 * Interface BankingStrategyInterface
 * Định nghĩa các phương thức cần thiết cho một giải pháp nạp tiền ngân hàng/ví điện tử
 */
interface BankingStrategyInterface
{
    /**
     * Lấy thông tin thanh toán (Số TK, Chủ TK, Ngân hàng, Nội dung, QR Code)
     * 
     * @param int $amount - Số tiền nạp
     * @param string $orderCode - Mã đơn nạp duy nhất
     * @return array
     */
    public function getPaymentDetails(int $amount, string $orderCode): array;

    /**
     * Xác thực dữ liệu Webhook từ nhà cung cấp
     * 
     * @param array $data - Dữ liệu nhận được từ Webhook
     * @param array $headers - Headers đi kèm (nếu cần check signature)
     * @return bool
     */
    public function verifyWebhook(array $data, array $headers): bool;

    /**
     * Xử lý dữ liệu Webhook (Trích xuất: mã đơn, số tiền thực nhận)
     * 
     * @param array $data
     * @return array ['order_code' => string, 'amount' => int, 'transaction_id' => string]
     */
    public function parseWebhookData(array $data): array;
}
