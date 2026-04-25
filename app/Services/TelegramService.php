<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Settings;

/**
 * Class TelegramService
 * Service gửi thông báo qua Telegram Bot API
 *
 * REFACTORED: buildOrderMessage() thay switch/case bằng data-driven lookup array
 * → Thêm sản phẩm mới chỉ cần thêm 1 entry vào $templates
 */
class TelegramService
{
    protected $botToken;
    protected $adminChatId;
    protected $apiUrl;

    public function __construct()
    {
        $settings = Settings::getOne();
        if ($settings) {
            $this->botToken     = $settings->telegram_bot_token    ?? '';
            $this->adminChatId  = $settings->telegram_admin_chat_id ?? '';
        } else {
            $this->botToken    = config('services.telegram.bot_token', '');
            $this->adminChatId = config('services.telegram.admin_chat_id', '');
        }

        $this->apiUrl = "https://api.telegram.org/bot{$this->botToken}";
    }

    /**
     * Gửi tin nhắn đến Telegram chat
     */
    public function sendMessage(string $chatId, string $message, string $parseMode = 'HTML', ?array $replyMarkup = null): array
    {
        if (empty($this->botToken)) {
            Log::warning('Telegram bot token not configured');
            return ['success' => false, 'message' => 'Telegram bot token not configured'];
        }

        try {
            $data = [
                'chat_id'    => $chatId,
                'text'       => $message,
                'parse_mode' => $parseMode
            ];

            if ($replyMarkup !== null) {
                $data['reply_markup'] = json_encode($replyMarkup);
            }

            $response = Http::timeout(10)->post("{$this->apiUrl}/sendMessage", $data);

            if ($response->successful()) {
                $result = $response->json();
                if ($result['ok'] ?? false) {
                    return ['success' => true, 'message' => 'Message sent successfully'];
                }
                Log::error('Telegram API returned error', ['result' => $result]);
                return ['success' => false, 'message' => $result['description'] ?? 'Unknown error'];
            }

            Log::error('Telegram API HTTP error', ['status' => $response->status(), 'body' => $response->body()]);
            return ['success' => false, 'message' => 'HTTP error: ' . $response->status()];

        } catch (\Exception $e) {
            Log::error('Telegram API Exception', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return ['success' => false, 'message' => 'Exception: ' . $e->getMessage()];
        }
    }

    /**
     * Trả lời callback query từ Telegram
     */
    public function answerCallbackQuery(string $callbackQueryId, string $text, bool $showAlert = false): array
    {
        if (empty($this->botToken)) {
            return ['success' => false, 'message' => 'Telegram bot token not configured'];
        }

        try {
            $response = Http::timeout(10)->post("{$this->apiUrl}/answerCallbackQuery", [
                'callback_query_id' => $callbackQueryId,
                'text'              => $text,
                'show_alert'        => $showAlert
            ]);

            if ($response->successful()) {
                $result = $response->json();
                if ($result['ok'] ?? false) return ['success' => true, 'message' => 'Callback answered'];
                Log::error('Telegram answerCallbackQuery error', ['result' => $result]);
                return ['success' => false, 'message' => $result['description'] ?? 'Unknown error'];
            }

            Log::error('Telegram answerCallbackQuery HTTP error', ['status' => $response->status()]);
            return ['success' => false, 'message' => 'HTTP error: ' . $response->status()];

        } catch (\Exception $e) {
            Log::error('Telegram answerCallbackQuery Exception', ['message' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Exception: ' . $e->getMessage()];
        }
    }

    /**
     * Chỉnh sửa tin nhắn đã gửi
     */
    public function editMessageText(string $chatId, int $messageId, string $text, string $parseMode = 'HTML', ?array $replyMarkup = null): array
    {
        if (empty($this->botToken)) {
            return ['success' => false, 'message' => 'Telegram bot token not configured'];
        }

        try {
            $data = [
                'chat_id'    => $chatId,
                'message_id' => $messageId,
                'text'       => $text,
                'parse_mode' => $parseMode
            ];

            if ($replyMarkup !== null) {
                $data['reply_markup'] = json_encode($replyMarkup);
            }

            $response = Http::timeout(10)->post("{$this->apiUrl}/editMessageText", $data);

            if ($response->successful()) {
                $result = $response->json();
                if ($result['ok'] ?? false) return ['success' => true, 'message' => 'Message edited'];
                Log::error('Telegram editMessageText error', ['result' => $result]);
                return ['success' => false, 'message' => $result['description'] ?? 'Unknown error'];
            }

            Log::error('Telegram editMessageText HTTP error', ['status' => $response->status()]);
            return ['success' => false, 'message' => 'HTTP error: ' . $response->status()];

        } catch (\Exception $e) {
            Log::error('Telegram editMessageText Exception', ['message' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Exception: ' . $e->getMessage()];
        }
    }

    /**
     * Thông báo cho admin về đơn hàng mới
     */
    public function notifyNewOrder(string $orderType, array $orderDetails): array
    {
        if (empty($this->adminChatId)) {
            Log::warning('Telegram admin chat ID not configured');
            return ['success' => false, 'message' => 'Admin chat ID not configured'];
        }

        $message = $this->buildOrderMessage($orderType, $orderDetails);
        return $this->sendMessage($this->adminChatId, $message);
    }

    /**
     * Thông báo cho admin về feedback mới
     */
    public function notifyNewFeedback(array $feedbackDetails): array
    {
        if (empty($this->adminChatId)) {
            Log::warning('Telegram admin chat ID not configured');
            return ['success' => false, 'message' => 'Admin chat ID not configured'];
        }

        $message     = $this->buildFeedbackMessage($feedbackDetails);
        $feedbackId  = $feedbackDetails['feedback_id'] ?? null;
        $inlineKeyboard = null;

        if ($feedbackId) {
            $inlineKeyboard = [
                'inline_keyboard' => [[
                    ['text' => '✅ Đã hỗ trợ', 'callback_data' => 'feedback_done_' . $feedbackId]
                ]]
            ];
        }

        return $this->sendMessage($this->adminChatId, $message, 'HTML', $inlineKeyboard);
    }

    /**
     * Xây dựng tin nhắn thông báo đơn hàng
     * Data-driven: thêm sản phẩm mới chỉ cần thêm 1 entry vào $templates
     */
    protected function buildOrderMessage(string $orderType, array $orderDetails): string
    {
        $username = $orderDetails['username'] ?? 'N/A';
        $mgd      = $orderDetails['mgd']      ?? 'N/A';
        $time     = $orderDetails['time']     ?? date('d/m/Y - H:i:s');

        $templates = [
            'domain' => fn() =>
                "🌐 <b>ĐƠN HÀNG MỚI - DOMAIN</b>\n\n" .
                "👤 Khách hàng: <code>{$username}</code>\n" .
                "🔖 Mã GD: <code>{$mgd}</code>\n" .
                "🌍 Tên miền: <code>" . ($orderDetails['domain'] ?? 'N/A') . "</code>\n" .
                "🔧 NS1: <code>" . ($orderDetails['ns1'] ?? 'N/A') . "</code>\n" .
                "🔧 NS2: <code>" . ($orderDetails['ns2'] ?? 'N/A') . "</code>\n" .
                "⏰ {$time}",

            'hosting' => fn() =>
                "🖥️ <b>ĐƠN HÀNG MỚI - HOSTING</b>\n\n" .
                "👤 Khách hàng: <code>{$username}</code>\n" .
                "🔖 Mã GD: <code>{$mgd}</code>\n" .
                "📦 Gói: <b>" . ($orderDetails['product_name'] ?? 'N/A') . "</b>\n" .
                "⏳ Thời hạn: " . ($orderDetails['period'] ?? 'N/A') . "\n" .
                "⏰ {$time}",

            'vps' => fn() =>
                "💻 <b>ĐƠN HÀNG MỚI - VPS</b>\n\n" .
                "👤 Khách hàng: <code>{$username}</code>\n" .
                "🔖 Mã GD: <code>{$mgd}</code>\n" .
                "📦 Gói: <b>" . ($orderDetails['product_name'] ?? 'N/A') . "</b>\n" .
                "⏳ Thời hạn: " . ($orderDetails['period'] ?? 'N/A') . "\n" .
                "⏰ {$time}",

            'sourcecode' => fn() =>
                "📦 <b>ĐƠN HÀNG MỚI - SOURCE CODE</b>\n\n" .
                "👤 Khách hàng: <code>{$username}</code>\n" .
                "🔖 Mã GD: <code>{$mgd}</code>\n" .
                "📝 Sản phẩm: <b>" . ($orderDetails['product_name'] ?? 'N/A') . "</b>\n" .
                "⏰ {$time}",
        ];

        if (isset($templates[$orderType])) {
            return ($templates[$orderType])();
        }

        // Default fallback
        return "📋 <b>ĐƠN HÀNG MỚI</b>\n\n" .
               "👤 Khách hàng: <code>{$username}</code>\n" .
               "🔖 Mã GD: <code>{$mgd}</code>\n" .
               "⏰ {$time}";
    }

    /**
     * Xây dựng tin nhắn thông báo feedback
     */
    protected function buildFeedbackMessage(array $feedbackDetails): string
    {
        $feedbackId = $feedbackDetails['feedback_id'] ?? 'N/A';
        $username   = $feedbackDetails['username']    ?? 'N/A';
        $email      = $feedbackDetails['email']       ?? 'N/A';
        $title      = $feedbackDetails['title']       ?? 'N/A';
        $content    = $feedbackDetails['content']     ?? 'N/A';
        $time       = $feedbackDetails['time']        ?? date('d/m/Y - H:i:s');

        if (strlen($content) > 300) {
            $content = substr($content, 0, 300) . '...';
        }

        return "💬 <b>PHẢN HỒI MỚI</b>\n\n" .
               "🆔 ID: <code>#{$feedbackId}</code>\n" .
               "👤 Tài khoản: <code>{$username}</code>\n" .
               "📧 Email: <code>{$email}</code>\n" .
               "📌 Tiêu đề: <b>{$title}</b>\n" .
               "📝 Nội dung:\n{$content}\n\n" .
               "⏰ Thời gian: {$time}";
    }
}
