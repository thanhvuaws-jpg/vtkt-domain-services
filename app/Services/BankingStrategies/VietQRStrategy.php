<?php

namespace App\Services\BankingStrategies;

use App\Contracts\BankingStrategyInterface;
use Illuminate\Support\Facades\Log;

class VietQRStrategy implements BankingStrategyInterface
{
    protected $bankId;
    protected $accountNo;
    protected $accountName;
    protected $template = 'compact'; // compact, qr_only, etc.

    public function __construct()
    {
        // Ưu tiên đọc từ Settings, fallback cứng để test (sẽ replace bằng thông tin sếp)
        $settings = \App\Models\Settings::getOne();
        $this->bankId = $settings->bank_id ?? 'BIDV';
        $this->accountNo = $settings->bank_account_no ?? '6151099464';
        $this->accountName = $settings->bank_account_name ?? 'DAM THANH VU';
    }

    public function getPaymentDetails(int $amount, string $orderCode): array
    {
        // URL tạo QR động theo chuẩn VietQR.io
        $qrUrl = "https://img.vietqr.io/image/{$this->bankId}-{$this->accountNo}-{$this->template}.png";
        $qrUrl .= "?amount={$amount}&addInfo=" . urlencode($orderCode) . "&accountName=" . urlencode($this->accountName);

        return [
            'method' => 'BANKING',
            'bank_name' => $this->bankId,
            'account_no' => $this->accountNo,
            'account_name' => $this->accountName,
            'amount' => $amount,
            'content' => $orderCode,
            'qr_code' => $qrUrl
        ];
    }

    public function verifyWebhook(array $data, array $headers): bool
    {
        // Tùy theo bên cung cấp (SePay, Casso...)
        // Ở đây giả lập kiểm tra sự tồn tại của các trường cơ bản
        return isset($data['amount']) && (isset($data['description']) || isset($data['content']));
    }

    public function parseWebhookData(array $data): array
    {
        // Trích xuất mã đơn từ nội dung chuyển khoản
        // Giả sử nội dung là: Admin_VUDZ123 hoặc Nap_ID_123
        $content = $data['description'] ?? $data['content'] ?? '';
        
        return [
            'order_code' => $content, // Cần logic Regex nếu mã đơn nằm trong chuỗi dài
            'amount' => (int)$data['amount'],
            'transaction_id' => $data['transaction_id'] ?? $data['id'] ?? uniqid(),
        ];
    }
}
