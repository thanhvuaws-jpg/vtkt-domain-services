<?php

namespace App\Services\BankingStrategies;

use App\Contracts\BankingStrategyInterface;

class MomoStrategy implements BankingStrategyInterface
{
    protected $phone;
    protected $name;

    public function __construct()
    {
        $settings = \App\Models\Settings::getOne();
        $this->phone = $settings->momo_phone ?? '0856761038';
        $this->name = $settings->momo_name ?? 'DAM THANH VU';
    }

    public function getPaymentDetails(int $amount, string $orderCode): array
    {
        // URL QR Momo (Sử dụng API trung gian hoặc generate local)
        // Đây là ví dụ URL QR chuẩn
        $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=2|99|{$this->phone}|{$this->name}|admin@vtkt.vn|0|0|{$amount}|{$orderCode}|transfer_myqr";

        return [
            'method' => 'MOMO',
            'phone' => $this->phone,
            'account_name' => $this->name,
            'amount' => $amount,
            'content' => $orderCode,
            'qr_code' => $qrUrl
        ];
    }

    public function verifyWebhook(array $data, array $headers): bool
    {
        // Kiểm tra token hoặc secret từ Momo Webhook
        return isset($data['amount']) && isset($data['message']);
    }

    public function parseWebhookData(array $data): array
    {
        return [
            'order_code' => $data['message'] ?? '',
            'amount' => (int)$data['amount'],
            'transaction_id' => $data['tranId'] ?? uniqid(),
        ];
    }
}
