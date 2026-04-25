<?php

namespace App\Services;

use App\Contracts\BankingStrategyInterface;
use App\Models\Deposit;
use App\Models\User;
use App\Services\BankingStrategies\VietQRStrategy;
use App\Services\BankingStrategies\MomoStrategy;
use Illuminate\Support\Facades\Log;

class BankingService
{
    protected $strategies = [];

    public function __construct()
    {
        $this->strategies['BANKING'] = new VietQRStrategy();
        $this->strategies['MOMO'] = new MomoStrategy();
    }

    /**
     * Lấy chiến lược xử lý theo phương thức
     */
    public function getStrategy(string $method): ?BankingStrategyInterface
    {
        return $this->strategies[strtoupper($method)] ?? null;
    }

    /**
     * Xử lý Webhook chung cho tất cả các phương thức
     */
    public function handleWebhook(string $method, array $data, array $headers = []): array
    {
        $strategy = $this->getStrategy($method);
        if (!$strategy) {
            return ['success' => false, 'message' => 'Phương thức không hỗ trợ'];
        }

        if (!$strategy->verifyWebhook($data, $headers)) {
            Log::warning("Webhook verification failed for {$method}", ['data' => $data]);
            return ['success' => false, 'message' => 'Xác thực Webhook thất bại'];
        }

        $parsed = $strategy->parseWebhookData($data);
        $orderCode = $parsed['order_code'];
        $amount = $parsed['amount'];
        $transactionId = $parsed['transaction_id'];

        // Logic tìm User dựa trên nội dung chuyển khoản
        // Ví dụ: Admin_VUDZ123 -> User ID là 123
        $userId = $this->extractUserId($orderCode);
        if (!$userId) {
            Log::error("Cannot extract User ID from content: {$orderCode}");
            return ['success' => false, 'message' => 'Không tìm thấy ID người dùng trong nội dung'];
        }

        $user = User::find($userId);
        if (!$user) {
            return ['success' => false, 'message' => 'Người dùng không tồn tại'];
        }

        // Lưu lịch sử nạp tiền vào bảng deposits
        $deposit = Deposit::create([
            'code' => $transactionId,
            'amount' => $amount,
            'user_id' => $userId,
            'status' => 1 // Thành công luôn vì đây là Webhook báo nạp thành công
        ]);

        // Cộng tiền cho User
        $oldBalance = $user->tien;
        $user->tien += $amount;
        $user->save();

        Log::info("Auto Recharge Success: User #{$userId} | +{$amount} | New Balance: {$user->tien}");

        // Thông báo Telegram cho Admin qua TelegramService
        $this->notifyAdmin($user, $amount, $method, $transactionId);

        return [
            'success' => true,
            'message' => 'Nạp tiền thành công',
            'amount' => $amount,
            'user' => $user->taikhoan
        ];
    }

    /**
     * Trích xuất User ID từ nội dung chuyển khoản
     */
    protected function extractUserId(string $content): ?int
    {
        // Regex bắt số ở cuối chuỗi Admin_VUDZ... sau khi đã strip các ký tự thừa
        if (preg_match('/(\d+)$/', $content, $matches)) {
            return (int)$matches[1];
        }
        return null;
    }

    /**
     * Thông báo cho Admin
     */
    protected function notifyAdmin($user, $amount, $method, $transactionId)
    {
        try {
            $telegramService = app(TelegramService::class);
            $settings = \App\Models\Settings::getOne();
            $adminChatId = $settings->telegram_admin_chat_id;

            if ($adminChatId) {
                $text = "💰 <b>BIẾN ĐỘNG SỐ DƯ (AUTO)</b>\n";
                $text .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
                $text .= "👤 Khách: <code>{$user->taikhoan}</code>\n";
                $text .= "➕ Số tiền: <b>+" . number_format($amount) . " ₫</b>\n";
                $text .= "💳 Phương thức: <b>{$method}</b>\n";
                $text .= "🔖 Mã GD: <code>{$transactionId}</code>\n";
                $text .= "⏰ Thời gian: " . date('H:i:s d/m/Y');

                $telegramService->sendMessage($adminChatId, $text, 'HTML');
            }
        } catch (\Exception $e) {
            Log::error("Failed to send Telegram notification for deposit: " . $e->getMessage());
        }
    }
}
