<?php
// Khai báo namespace cho Controller này - thuộc App\Http\Controllers
namespace App\Http\Controllers;

// Import các Model và Service cần thiết
use App\Models\User; // Model quản lý người dùng
use App\Models\Feedback; // Model quản lý feedback
use App\Services\TelegramService; // Service gửi thông báo Telegram
use Illuminate\Http\Request; // Class xử lý HTTP request
use Illuminate\Support\Facades\Log; // Facade để ghi log

/**
 * Class TelegramWebhookController
 * Controller xử lý webhook từ Telegram Bot
 * Nhận và xử lý các tin nhắn từ Telegram để tạo feedback
 */
class TelegramWebhookController extends Controller
{
    // Thuộc tính lưu trữ instance của TelegramService
    protected $telegramService;

    /**
     * Hàm khởi tạo (Constructor)
     * Dependency Injection: Laravel tự động inject TelegramService vào đây
     * 
     * @param TelegramService $telegramService - Service để gửi thông báo Telegram
     */
    public function __construct(TelegramService $telegramService)
    {
        // Gán TelegramService vào thuộc tính của class
        $this->telegramService = $telegramService;
    }

    /**
     * Hiển thị thông tin webhook khi truy cập bằng GET (browser)
     * 
     * @return \Illuminate\Http\Response - Response HTML thân thiện
     */
    public function info()
    {
        $settings = \App\Models\Settings::getOne();
        $botToken = $settings->telegram_bot_token ?? config('services.telegram.bot_token', '');
        $adminChatId = $settings->telegram_admin_chat_id ?? config('services.telegram.admin_chat_id', '');
        
        $webhookUrl = config('app.url', '') . '/telegram/webhook';
        if (strpos($webhookUrl, 'http://') === 0) {
            $webhookUrl = str_replace('http://', 'https://', $webhookUrl);
        }
        
        $html = '<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Telegram Webhook - VTKT</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .icon {
            text-align: center;
            font-size: 64px;
            margin-bottom: 20px;
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 10px;
            font-size: 28px;
        }
        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            font-size: 16px;
        }
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .info-box h2 {
            color: #667eea;
            font-size: 18px;
            margin-bottom: 15px;
        }
        .info-item {
            margin: 10px 0;
            color: #555;
        }
        .info-item strong {
            color: #333;
        }
        .status {
            text-align: center;
            padding: 15px;
            background: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 8px;
            color: #155724;
            margin-top: 20px;
            font-weight: 500;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">🤖</div>
        <h1>Telegram Webhook</h1>
        <p class="subtitle">Hệ thống quản lý Telegram Bot</p>
        
        <div class="info-box">
            <h2>📋 Thông tin Webhook</h2>
            <div class="info-item">
                <strong>URL:</strong> <code>' . htmlspecialchars($webhookUrl) . '</code>
            </div>
            <div class="info-item">
                <strong>Method:</strong> POST (Telegram API)
            </div>
            <div class="info-item">
                <strong>Status:</strong> ' . (!empty($botToken) ? '✅ Đã cấu hình' : '❌ Chưa cấu hình') . '
            </div>
        </div>
        
        <div class="status">
            ✅ Webhook đang hoạt động bình thường
        </div>
        
        <div class="warning">
            ⚠️ Lưu ý: Trang này chỉ để kiểm tra. Webhook chỉ chấp nhận POST request từ Telegram API.
        </div>
    </div>
</body>
</html>';
        
        return response($html, 200)->header('Content-Type', 'text/html; charset=utf-8');
    }

    /**
     * Xử lý webhook đến từ Telegram
     * Nhận dữ liệu từ Telegram và xử lý message hoặc callback query
     * 
     * @param Request $request - HTTP request chứa dữ liệu webhook từ Telegram
     * @return \Illuminate\Http\Response - Response HTTP (200 OK hoặc lỗi)
     */
    public function handle(Request $request)
    {
        try {
            // Ghi log dữ liệu webhook để debug
            Log::info('Telegram webhook received', [
                'data' => $request->all() // Tất cả dữ liệu từ request
            ]);

            // Lấy tất cả dữ liệu từ request
            $update = $request->all();

            // Kiểm tra dữ liệu không được rỗng
            if (empty($update)) {
                Log::warning('Telegram webhook: Empty update data');
                return response('Invalid request', 400); // HTTP 400 Bad Request
            }

            // Lấy message và callback_query từ update
            $message = $update['message'] ?? null; // Tin nhắn từ user
            $callbackQuery = $update['callback_query'] ?? null; // Callback query (cho button inline)

            // Xử lý tin nhắn nếu có
            if ($message) {
                $this->processMessage($message);
            }

            // Xử lý callback query nếu có (dùng trong tương lai)
            if ($callbackQuery) {
                $this->processCallbackQuery($callbackQuery);
            }

            // Trả về HTTP 200 OK để Telegram biết đã nhận được webhook
            return response('OK', 200);
        } catch (\Exception $e) {
            // Ghi log lỗi nếu có exception
            Log::error('Telegram webhook error', [
                'message' => $e->getMessage(), // Thông báo lỗi
                'trace' => $e->getTraceAsString() // Stack trace
            ]);

            // Trả về HTTP 500 Internal Server Error
            return response('Error', 500);
        }
    }

    /**
     * Xử lý tin nhắn đến từ Telegram
     * Phân tích tin nhắn và xử lý các lệnh (/start, /help) hoặc tạo feedback
     * 
     * @param array $message - Mảng chứa thông tin tin nhắn từ Telegram
     * @return void
     */
    protected function processMessage(array $message): void
    {
        // Lấy thông tin từ message
        $chatId = $message['chat']['id'] ?? null; // ID chat (dùng để gửi tin nhắn lại)
        $text = $message['text'] ?? ''; // Nội dung tin nhắn
        $from = $message['from'] ?? []; // Thông tin người gửi
        $username = $from['username'] ?? $from['first_name'] ?? 'Unknown'; // Username hoặc tên

        // Kiểm tra chat ID có tồn tại không
        if (!$chatId) {
            Log::warning('Telegram message: Missing chat ID');
            return; // Thoát nếu không có chat ID
        }

        // Ghi log tin nhắn đã nhận
        Log::info('Telegram message received', [
            'chat_id' => $chatId, // ID chat
            'username' => $username, // Username
            'text' => $text // Nội dung tin nhắn
        ]);

        // Xử lý lệnh /start
        if (strpos($text, '/start') === 0) {
            $this->handleStartCommand($chatId);
            return;
        }

        // Xử lý lệnh /menu - gọi menu chính
        if (strpos($text, '/menu') === 0) {
            $this->handleStartCommand($chatId); // Dùng lại hàm handleStartCommand để hiển thị menu
            return;
        }

        // Xử lý lệnh /help
        if (strpos($text, '/help') === 0) {
            $this->handleHelpCommand($chatId);
            return;
        }

        // Nếu không phải lệnh, thông báo bot chỉ dùng để admin nhận thông báo
        $adminChatId = config('services.telegram.admin_chat_id');
        $settings = \App\Models\Settings::getOne();
        if ($settings && !empty($settings->telegram_admin_chat_id)) {
            $adminChatId = $settings->telegram_admin_chat_id;
        }
        
        // Chỉ admin mới có thể tương tác, user khác chỉ nhận thông báo
        if ($chatId != $adminChatId) {
            $message = "ℹ️ Bot này chỉ dùng để Admin nhận thông báo.\n\n" .
                       "Để gửi phản hồi, vui lòng sử dụng form trên website:\n" .
                       "https://vtkt.online/feedback";
            $this->telegramService->sendMessage($chatId, $message);
            return;
        }
        
        // Nếu là admin, kiểm tra các lệnh đặc biệt
        if ($chatId == $adminChatId) {
            // Xử lý các nút bấm từ Reply Keyboard
            switch ($text) {
                case '📊 Thống kê':
                    $this->handleUserStats($chatId, null, []);
                    return;
                case '📦 Đơn hàng':
                    $this->handleNewOrders($chatId, null, []);
                    return;
                case '🎁 Voucher':
                    $this->handleVoucherManagement($chatId);
                    return;
                case '🛠️ Cài đặt':
                    $this->handleSettingsMenu($chatId);
                    return;
            }

            // Xử lý lệnh cộng tiền: congtien:username:amount
            if (preg_match('/^congtien:([^:]+):(\d+)$/i', $text, $matches)) {
                $this->processAddBalance($chatId, $matches[1], $matches[2]);
                return;
            }
            
            // Xử lý lệnh cập nhật DNS: updatedns:domain:ns1:ns2
            if (preg_match('/^updatedns:([^:]+):([^:]+):([^:]+)$/i', $text, $matches)) {
                $this->processUpdateDNS($chatId, $matches[1], $matches[2], $matches[3]);
                return;
            }
            
            // Xử lý lệnh gửi phản hồi: reply:feedbackId:nội dung
            if (preg_match('/^reply:(\d+):(.+)$/i', $text, $matches)) {
                $feedbackId = $matches[1];
                $replyText = $matches[2];
                $this->processReplyFeedback($chatId, $feedbackId, $replyText);
                return;
            }

            // Xử lý lệnh cập nhật thông báo toàn trang: broadcast:nội dung
            if (preg_match('/^broadcast:(.+)$/is', $text, $matches)) {
                $this->processUpdateBroadcast($chatId, $matches[1]);
                return;
            }

            // Xử lý lệnh duyệt đơn: duyệt:id | hủy:id
            if (preg_match('/^(duyệt|huy|hủy):(\d+)$/i', $text, $matches)) {
                $action = strtolower($matches[1]);
                $orderId = $matches[2];
                $this->processOrderAction($chatId, $orderId, ($action == 'duyệt' ? 1 : 2));
                return;
            }
        }
        
        // Nếu là admin nhưng gửi tin nhắn không phải lệnh, không xử lý
        Log::info('Admin sent non-command message', ['chat_id' => $chatId, 'text' => $text]);
    }

    /**
     * Xử lý lệnh /start từ Telegram
     * Thông báo bot chỉ dùng để admin nhận thông báo
     * 
     * @param string $chatId - ID chat để gửi tin nhắn
     * @return void
     */
    protected function handleStartCommand(string $chatId): void
    {
        // Kiểm tra xem có phải admin không (so sánh với admin chat ID)
        $adminChatId = config('services.telegram.admin_chat_id');
        $settings = \App\Models\Settings::getOne();
        if ($settings && !empty($settings->telegram_admin_chat_id)) {
            $adminChatId = $settings->telegram_admin_chat_id;
        }
        
        // Nếu là admin, hiển thị menu chính
        if ($chatId == $adminChatId) {
            $message = "👋 <b>CHÀO MỪNG ADMIN!</b>\n\n" .
                       "Hệ thống đã sẵn sàng điều khiển. Sếp có thể dùng bàn phím bên dưới để quản lý nhanh hoặc chọn menu chi tiết:";
            
            // 1. Reply Keyboard (Bàn phím chính cố định)
            $replyKeyboard = [
                'keyboard' => [
                    [['text' => '📊 Thống kê'], ['text' => '📦 Đơn hàng']],
                    [['text' => '🎁 Voucher'], ['text' => '🛠️ Cài đặt']]
                ],
                'resize_keyboard' => true,
                'one_time_keyboard' => false
            ];
            
            // 2. Inline Keyboard (Menu chi tiết)
            $inlineKeyboard = [
                'inline_keyboard' => [
                    [['text' => '💬 Feedback chờ xử lý', 'callback_data' => 'menu_pending_feedback']],
                    [['text' => '🌐 Cửa hàng (Landing)', 'url' => config('app.url')]]
                ]
            ];
            
            // Gửi tin nhắn kèm cả 2 loại bàn phím
            // Lưu ý: Telegram chỉ hỗ trợ 1 cái chính (thường là Inline hoặc Reply gộp vào reply_markup)
            // Ở đây ta gửi tin có reply_keyboard trước để kích hoạt bàn phím chính
            $this->telegramService->sendMessage($chatId, $message, 'HTML', $replyKeyboard);
            
            // Sau đó gửi menu inline
            $this->telegramService->sendMessage($chatId, "🛠️ <b>Bảng điều khiển chi tiết:</b>", 'HTML', $inlineKeyboard);
            return;
        } else {
            // Nếu không phải admin, thông báo bot chỉ dùng để admin nhận thông báo
            $message = "ℹ️ <b>Thông báo</b>\n\n" .
                       "Bot này chỉ dùng để Admin nhận thông báo về feedback và đơn hàng.\n\n" .
                       "Để gửi phản hồi, vui lòng sử dụng form trên website:\n" .
                       "https://vtkt.online/feedback";

            // Gửi tin nhắn qua TelegramService
            $this->telegramService->sendMessage($chatId, $message);
            return;
        }
    }

    /**
     * Xử lý lệnh /help từ Telegram
     * Gửi hướng dẫn
     * 
     * @param string $chatId - ID chat để gửi tin nhắn
     * @return void
     */
    protected function handleHelpCommand(string $chatId): void
    {
        // Kiểm tra xem có phải admin không
        $adminChatId = config('services.telegram.admin_chat_id');
        $settings = \App\Models\Settings::getOne();
        if ($settings && !empty($settings->telegram_admin_chat_id)) {
            $adminChatId = $settings->telegram_admin_chat_id;
        }
        
        if ($chatId == $adminChatId) {
            $message = "📋 <b>HƯỚNG DẪN CHO ADMIN</b>\n\n" .
                       "Bot này tự động gửi thông báo về:\n" .
                       "• Feedback mới từ khách hàng\n" .
                       "• Đơn hàng mới\n\n" .
                       "Khi nhận được thông báo feedback, bạn có thể:\n" .
                       "• Click nút '✅ Đã hỗ trợ' để đánh dấu đã xử lý\n" .
                       "• Xem chi tiết trên Admin Panel";
        } else {
            $message = "📋 <b>HƯỚNG DẪN</b>\n\n" .
                       "Bot này chỉ dùng để Admin nhận thông báo.\n\n" .
                       "Để gửi phản hồi, vui lòng:\n" .
                       "1. Truy cập: https://vtkt.online/feedback\n" .
                       "2. Điền form phản hồi\n" .
                       "3. Gửi phản hồi";
        }

        // Gửi tin nhắn qua TelegramService
        $this->telegramService->sendMessage($chatId, $message);
    }

    /**
     * Xử lý tin nhắn feedback từ user
     * Phân tích email và nội dung feedback, sau đó lưu vào database
     * 
     * @param string $chatId - ID chat để gửi tin nhắn phản hồi
     * @param string $text - Nội dung tin nhắn từ user
     * @param array $from - Thông tin người gửi (username, first_name, etc.)
     * @return void
     */
    protected function processFeedbackMessage(string $chatId, string $text, array $from): void
    {
        // Phân tích email và nội dung feedback từ tin nhắn
        $lines = explode("\n", $text); // Tách tin nhắn thành các dòng
        $email = ''; // Email được tìm thấy
        $feedbackMessage = ''; // Nội dung feedback

        // Tìm email trong tin nhắn
        foreach ($lines as $line) {
            $line = trim($line); // Loại bỏ khoảng trắng đầu cuối
            // Kiểm tra dòng có phải là email hợp lệ không
            if (filter_var($line, FILTER_VALIDATE_EMAIL)) {
                $email = $line; // Lưu email
                break; // Dừng khi tìm thấy email đầu tiên
            }
        }

        // Nếu không tìm thấy email hợp lệ, dùng dòng đầu tiên làm email (có thể không đúng format)
        if (empty($email) && !empty($lines[0])) {
            $email = trim($lines[0]);
        }

        // Lấy nội dung feedback (loại trừ dòng email)
        $feedbackLines = []; // Mảng chứa các dòng feedback
        $foundEmail = false; // Biến đánh dấu đã tìm thấy email chưa
        foreach ($lines as $line) {
            $line = trim($line);
            // Nếu dòng này là email, đánh dấu đã tìm thấy email và bỏ qua dòng này
            if (filter_var($line, FILTER_VALIDATE_EMAIL)) {
                $foundEmail = true;
                continue; // Bỏ qua dòng email
            }
            // Nếu đã tìm thấy email hoặc dòng không rỗng, thêm vào feedback
            if ($foundEmail || !empty($line)) {
                $feedbackLines[] = $line;
            }
        }

        // Ghép các dòng feedback thành một chuỗi
        $feedbackMessage = implode("\n", $feedbackLines);

        // Nếu không có nội dung feedback, dùng toàn bộ tin nhắn làm feedback
        if (empty($feedbackMessage)) {
            $feedbackMessage = $text;
        }

        // Tìm user trong database theo email
        $user = null; // User được tìm thấy
        $userId = 0; // ID user (mặc định: 0)
        $username = $from['first_name'] ?? $from['username'] ?? 'Unknown'; // Username từ Telegram

        // Nếu có email hợp lệ, tìm user trong database
        if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $email)->first(); // Tìm user theo email
            if ($user) {
                $userId = $user->id; // Lấy ID user
                $username = $user->taikhoan; // Lấy username từ database
            }
        }

        // Nếu không tìm thấy user, dùng username từ Telegram làm email
        if (!$user) {
            $email = $email ?: ($from['username'] ?? '') . '@telegram';
        }

        // Lưu feedback vào database
        try {
            // Tạo chuỗi thời gian định dạng Việt Nam
            $time = date('d/m/Y - H:i:s');

            // Tạo feedback mới trong database
            $feedback = Feedback::create([
                'uid' => $userId, // ID user (0 nếu không tìm thấy)
                'username' => $username, // Username
                'email' => $email, // Email
                'message' => $feedbackMessage, // Nội dung feedback
                'telegram_chat_id' => (string)$chatId, // Chat ID từ Telegram (ép kiểu về string)
                'time' => $time, // Thời gian tạo
                'status' => 0 // Trạng thái: 0 = Chưa đọc
            ]);

            // Gửi tin nhắn xác nhận cho user
            $confirmMessage = "✅ Phản hồi của bạn đã được gửi thành công!\n\n" .
                            "Chúng tôi sẽ xem xét và phản hồi sớm nhất có thể.\n\n" .
                            "📧 Email: " . $email;

            $this->telegramService->sendMessage($chatId, $confirmMessage);

            // Gửi thông báo cho admin về feedback mới
            $this->telegramService->notifyNewFeedback([
                'username' => $username, // Username
                'title' => 'Phản hồi từ Telegram', // Tiêu đề
                'content' => $feedbackMessage, // Nội dung feedback
                'time' => $time // Thời gian
            ]);

            // Ghi log feedback đã được lưu thành công
            Log::info('Telegram feedback saved', [
                'feedback_id' => $feedback->id, // ID feedback
                'user_id' => $userId, // ID user
                'email' => $email // Email
            ]);
        } catch (\Exception $e) {
            // Ghi log lỗi nếu không lưu được feedback
            Log::error('Failed to save Telegram feedback', [
                'error' => $e->getMessage(), // Thông báo lỗi
                'chat_id' => $chatId // Chat ID
            ]);

            // Gửi tin nhắn lỗi cho user
            $errorMessage = "❌ Có lỗi xảy ra khi gửi phản hồi. Vui lòng thử lại sau.";
            $this->telegramService->sendMessage($chatId, $errorMessage);
        }
    }

    /**
     * Xử lý callback query từ Telegram
     * Callback query được gửi khi admin click vào button inline (ví dụ: "Đã hỗ trợ")
     * 
     * @param array $callbackQuery - Mảng chứa thông tin callback query từ Telegram
     * @return void
     */
    protected function processCallbackQuery(array $callbackQuery): void
    {
        // Lấy thông tin từ callback query
        $callbackQueryId = $callbackQuery['id'] ?? null; // ID callback query (dùng để answer)
        $from = $callbackQuery['from'] ?? []; // Thông tin người click
        $chatId = $from['id'] ?? null; // Chat ID của người click
        $data = $callbackQuery['data'] ?? ''; // Data từ button (ví dụ: feedback_done_123)
        $message = $callbackQuery['message'] ?? []; // Tin nhắn gốc chứa button

        // Ghi log callback query
        Log::info('Telegram callback query received', [
            'chat_id' => $chatId,
            'data' => $data,
            'callback_query_id' => $callbackQueryId
        ]);

        // Kiểm tra xem có phải admin không
        $adminChatId = config('services.telegram.admin_chat_id');
        $settings = \App\Models\Settings::getOne();
        if ($settings && !empty($settings->telegram_admin_chat_id)) {
            $adminChatId = $settings->telegram_admin_chat_id;
        }

        if ($chatId != $adminChatId) {
            // Nếu không phải admin, trả lời lỗi
            $this->telegramService->answerCallbackQuery($callbackQueryId, 'Chỉ admin mới có thể thực hiện hành động này.');
            return;
        }

        // Xử lý các menu item
        if ($data === 'menu_pending_feedback' || strpos($data, 'feedback_reply_') === 0 || strpos($data, 'feedback_mark_') === 0) {
            if ($data === 'menu_pending_feedback') {
                $this->showLoading($callbackQueryId, '⏳ Đang tải feedback chờ xử lý...');
            } elseif (strpos($data, 'feedback_reply_') === 0) {
                $this->showLoading($callbackQueryId, '⏳ Đang tải form phản hồi...');
            } else {
                $this->showLoading($callbackQueryId, '⏳ Đang xử lý...');
            }
            $this->handlePendingFeedback($chatId, $callbackQueryId, $message, $data);
            return;
        } elseif ($data === 'menu_processed_feedback') {
            $this->showLoading($callbackQueryId, '⏳ Đang tải feedback đã xử lý...');
            $this->handleProcessedFeedback($chatId, $callbackQueryId, $message);
            return;
        } elseif ($data === 'menu_user_stats' || strpos($data, 'user_stats_page_') === 0) {
            $this->showLoading($callbackQueryId, '⏳ Đang tải thống kê...');
            $this->handleUserStats($chatId, $callbackQueryId, $message);
            return;
        } elseif ($data === 'menu_add_balance' || strpos($data, 'add_balance_user_') === 0 || strpos($data, 'add_balance_amount_') === 0 || strpos($data, 'add_balance_page_') === 0) {
            if (strpos($data, 'add_balance_amount_') === 0) {
                $this->showLoading($callbackQueryId, '⏳ Đang cộng tiền...');
            } else {
                $this->showLoading($callbackQueryId, '⏳ Đang tải danh sách tài khoản...');
            }
            $this->handleAddBalance($chatId, $callbackQueryId, $message, $data);
            return;
        } elseif ($data === 'menu_update_dns' || strpos($data, 'update_dns_') === 0 || strpos($data, 'reject_dns_') === 0 || strpos($data, 'dns_update_') === 0 || strpos($data, 'dns_manual_') === 0) {
            if (strpos($data, 'reject_dns_') === 0) {
                $this->showLoading($callbackQueryId, '⏳ Đang từ chối yêu cầu...');
            } elseif (strpos($data, 'dns_update_') === 0) {
                $this->showLoading($callbackQueryId, '⏳ Đang cập nhật DNS...');
            } else {
                $this->showLoading($callbackQueryId, '⏳ Đang tải danh sách DNS...');
            }
            $this->handleUpdateDNS($chatId, $callbackQueryId, $message, $data);
            return;
        } elseif ($data === 'menu_new_orders') {
            $this->showLoading($callbackQueryId, '⏳ Đang tải đơn hàng...');
            $this->handleNewOrders($chatId, $callbackQueryId, $message);
            return;
        } elseif ($data === 'menu_help') {
            $this->showLoading($callbackQueryId, '⏳ Đang tải hướng dẫn...');
            $this->handleHelpCommand($chatId);
            $this->showSuccess($callbackQueryId, 'Đã hiển thị hướng dẫn');
            return;
        } elseif ($data === 'menu_voucher_stats') {
            $this->showLoading($callbackQueryId, '⏳ Đang tải thống kê Voucher...');
            $this->handleVoucherManagement($chatId);
            $this->showSuccess($callbackQueryId, 'Đã cập nhật thống kê');
            return;
        } elseif ($data === 'menu_settings') {
            $this->showLoading($callbackQueryId, '⏳ Đang tải cài đặt...');
            $this->handleSettingsMenu($chatId);
            return;
        } elseif ($data === 'toggle_maintenance') {
            $this->showLoading($callbackQueryId, '⏳ Đang thay đổi trạng thái...');
            $this->handleToggleMaintenance($chatId, $callbackQueryId, $message);
            return;
        } elseif ($data === 'edit_broadcast') {
            $this->showLoading($callbackQueryId, '📢 Nhập thông báo...');
            $this->handleEditBroadcast($chatId);
            return;
        } elseif ($data === 'menu_back') {
            // Quay về menu chính
            $this->showLoading($callbackQueryId, '⏳ Đang quay về menu...');
            $this->handleStartCommand($chatId);
            $this->showSuccess($callbackQueryId, 'Đã quay về menu chính');
            return;
        }
        
        // Xử lý callback "Đã hỗ trợ" feedback
        if (strpos($data, 'feedback_done_') === 0) {
            $feedbackId = str_replace('feedback_done_', '', $data);
            $this->showLoading($callbackQueryId, '⏳ Đang đánh dấu đã hỗ trợ...');
            
            // Cập nhật status feedback trong database
            try {
                $feedback = \App\Models\Feedback::find($feedbackId);
                if ($feedback) {
                    $feedback->status = 1; // Đánh dấu đã xử lý
                    $feedback->reply_time = date('d/m/Y - H:i:s'); // Thời gian xử lý
                    $feedback->save();

                    // Trả lời callback query thành công
                    $this->showSuccess($callbackQueryId, 'Đã đánh dấu feedback #' . $feedbackId . ' là đã hỗ trợ!');
                    
                    // Cập nhật tin nhắn để hiển thị đã xử lý
                    $messageId = $message['message_id'] ?? null;
                    if ($messageId) {
                        $updatedText = $message['text'] ?? '';
                        $updatedText .= "\n\n✅ <b>Đã xử lý</b> - " . date('d/m/Y H:i:s');
                        
                        // Cập nhật tin nhắn (xóa button)
                        $this->telegramService->editMessageText(
                            $chatId,
                            $messageId,
                            $updatedText
                        );
                    }

                    Log::info('Feedback marked as done', [
                        'feedback_id' => $feedbackId,
                        'admin_chat_id' => $chatId
                    ]);
                } else {
                    $this->showError($callbackQueryId, 'Không tìm thấy feedback này.', true);
                }
            } catch (\Exception $e) {
                Log::error('Error processing feedback callback', [
                    'error' => $e->getMessage(),
                    'feedback_id' => $feedbackId
                ]);
                $this->showError($callbackQueryId, 'Có lỗi xảy ra khi xử lý: ' . $e->getMessage(), true);
            }
        } else {
            // Callback không được nhận diện
            $this->showError($callbackQueryId, 'Hành động không hợp lệ.', false);
        }
    }

    /**
     * Xử lý xem feedback chờ xử lý
     */
    protected function handlePendingFeedback(string $chatId, ?string $callbackQueryId, array $message, string $data = 'menu_pending_feedback'): void
    {
        try {
            // Xử lý đánh dấu đã xử lý
            if (strpos($data, 'feedback_mark_') === 0) {
                $feedbackId = str_replace('feedback_mark_', '', $data);
                $feedback = \App\Models\Feedback::find($feedbackId);
                if (!$feedback) {
                    $this->showError($callbackQueryId, 'Không tìm thấy feedback', true);
                    return;
                }

                $feedback->status = 1;
                $feedback->reply_time = date('d/m/Y - H:i:s');
                $feedback->save();

                $text = "✅ <b>ĐÃ ĐÁNH DẤU ĐÃ XỬ LÝ</b>\n";
                $text .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
                $text .= "🆔 <b>Feedback ID:</b> #{$feedbackId}\n";
                $text .= "👤 <b>User:</b> <code>{$feedback->username}</code>\n";
                $text .= "⏰ <b>Thời gian:</b> " . date('d/m/Y H:i:s');

                $keyboard = [
                    'inline_keyboard' => [
                        [['text' => '🔄 Xem danh sách', 'callback_data' => 'menu_pending_feedback']],
                        [['text' => '🏠 Menu', 'callback_data' => 'menu_back']]
                    ]
                ];

                $messageId = $message['message_id'] ?? null;
                if ($messageId) {
                    $this->telegramService->editMessageText($chatId, $messageId, $text, 'HTML', $keyboard);
                } else {
                    $this->telegramService->sendMessage($chatId, $text, 'HTML', $keyboard);
                }
                $this->showSuccess($callbackQueryId, 'Đã đánh dấu đã xử lý!', false);
                return;
            }

            // Xử lý hiển thị form phản hồi
            if (strpos($data, 'feedback_reply_') === 0) {
                $feedbackId = str_replace('feedback_reply_', '', $data);
                $feedback = \App\Models\Feedback::find($feedbackId);
                if (!$feedback) {
                    $this->showError($callbackQueryId, 'Không tìm thấy feedback', true);
                    return;
                }

                $text = "💬 <b>GỬI PHẢN HỒI CHO USER</b>\n";
                $text .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
                $text .= "🆔 <b>Feedback ID:</b> #{$feedbackId}\n";
                $text .= "👤 <b>User:</b> <code>{$feedback->username}</code>\n";
                $text .= "📧 <b>Email:</b> <code>{$feedback->email}</code>\n\n";
                $text .= "📝 <b>Nội dung feedback:</b>\n";
                $text .= "{$feedback->message}\n\n";
                $text .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
                $text .= "Nhập phản hồi theo format:\n";
                $text .= "<code>reply:{$feedbackId}:nội dung phản hồi</code>\n\n";
                $text .= "Ví dụ:\n";
                $text .= "<code>reply:{$feedbackId}:Cảm ơn bạn đã phản hồi. Chúng tôi đã xử lý vấn đề của bạn.</code>";

                $keyboard = [
                    'inline_keyboard' => [
                        [['text' => '⬅️ Quay lại', 'callback_data' => 'menu_pending_feedback']]
                    ]
                ];

                $messageId = $message['message_id'] ?? null;
                if ($messageId) {
                    $this->telegramService->editMessageText($chatId, $messageId, $text, 'HTML', $keyboard);
                } else {
                    $this->telegramService->sendMessage($chatId, $text, 'HTML', $keyboard);
                }
                $this->showSuccess($callbackQueryId, 'Nhập phản hồi', false);
                return;
            }

            // Hiển thị danh sách feedback chờ xử lý
            $feedbacks = \App\Models\Feedback::where('status', 0)
                ->orderBy('id', 'desc')
                ->limit(10)
                ->get();

            if ($feedbacks->isEmpty()) {
                $text = "✅ <b>KHÔNG CÓ FEEDBACK CHỜ XỬ LÝ</b>\n\nTất cả feedback đã được xử lý!";
                $keyboard = [
                    'inline_keyboard' => [
                        [['text' => '🔄 Làm mới', 'callback_data' => 'menu_pending_feedback']],
                        [['text' => '🏠 Menu', 'callback_data' => 'menu_back']]
                    ]
                ];
            } else {
                $text = "📋 <b>FEEDBACK CHỜ XỬ LÝ</b> (" . $feedbacks->count() . ")\n";
                $text .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
                
                $keyboard = ['inline_keyboard' => []];
                foreach ($feedbacks as $feedback) {
                    $text .= "🆔 <b>#{$feedback->id}</b>\n";
                    $text .= "👤 <code>{$feedback->username}</code>\n";
                    $text .= "📧 <code>{$feedback->email}</code>\n";
                    $content = mb_substr($feedback->message, 0, 80);
                    if (mb_strlen($feedback->message) > 80) $content .= '...';
                    $text .= "📝 {$content}\n";
                    $text .= "⏰ {$feedback->time}\n";
                    $text .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

                    // Thêm 2 nút cho mỗi feedback với ID để dễ nhận biết
                    $keyboard['inline_keyboard'][] = [
                        ['text' => "✅ Xử lý #{$feedback->id}", 'callback_data' => 'feedback_mark_' . $feedback->id],
                        ['text' => "💬 Gửi phản hồi #{$feedback->id}", 'callback_data' => 'feedback_reply_' . $feedback->id]
                    ];
                }
                
                $keyboard['inline_keyboard'][] = [
                    ['text' => '🔄 Làm mới', 'callback_data' => 'menu_pending_feedback']
                ];
                $keyboard['inline_keyboard'][] = [
                    ['text' => '🏠 Menu', 'callback_data' => 'menu_back']
                ];
            }

            $messageId = $message['message_id'] ?? null;
            if ($messageId) {
                $this->telegramService->editMessageText($chatId, $messageId, $text, 'HTML', $keyboard);
            } else {
                $this->telegramService->sendMessage($chatId, $text, 'HTML', $keyboard);
            }
            if ($callbackQueryId) {
                $this->showSuccess($callbackQueryId, 'Đã tải danh sách feedback');
            }
        } catch (\Exception $e) {
            Log::error('Error handling pending feedback', ['error' => $e->getMessage()]);
            $this->showError($callbackQueryId, 'Có lỗi xảy ra: ' . $e->getMessage(), true);
        }
    }

    /**
     * Xử lý xem feedback đã xử lý
     */
    protected function handleProcessedFeedback(string $chatId, ?string $callbackQueryId, array $message): void
    {
        try {
            $feedbacks = \App\Models\Feedback::where('status', 1)
                ->orderBy('id', 'desc')
                ->limit(10)
                ->get();

            if ($feedbacks->isEmpty()) {
                $text = "📭 <b>CHƯA CÓ FEEDBACK NÀO ĐÃ XỬ LÝ</b>";
            } else {
                $text = "✅ <b>FEEDBACK ĐÃ XỬ LÝ</b> (" . $feedbacks->count() . ")\n\n";
                foreach ($feedbacks as $feedback) {
                    $text .= "🆔 <b>#{$feedback->id}</b>\n";
                    $text .= "👤 <code>{$feedback->username}</code>\n";
                    $text .= "⏰ Xử lý: {$feedback->reply_time}\n\n";
                }
            }

            $keyboard = [
                'inline_keyboard' => [
                    [['text' => '🔄 Làm mới', 'callback_data' => 'menu_processed_feedback']],
                    [['text' => '🏠 Về menu chính', 'callback_data' => 'menu_back']]
                ]
            ];

            $messageId = $message['message_id'] ?? null;
            if ($messageId) {
                $this->telegramService->editMessageText($chatId, $messageId, $text, 'HTML', $keyboard);
            } else {
                $this->telegramService->sendMessage($chatId, $text, 'HTML', $keyboard);
            }
            if ($callbackQueryId) {
                $this->showSuccess($callbackQueryId, 'Đã tải danh sách feedback');
            }
        } catch (\Exception $e) {
            Log::error('Error handling processed feedback', ['error' => $e->getMessage()]);
            $this->showError($callbackQueryId, 'Có lỗi xảy ra: ' . $e->getMessage(), true);
        }
    }

    /**
     * Xử lý thống kê tài khoản - hiển thị chi tiết từng tài khoản
     */
    protected function handleUserStats(string $chatId, ?string $callbackQueryId, array $message): void
    {
        try {
            $page = isset($message['text']) && preg_match('/page_(\d+)/', $message['text'], $matches) ? (int)$matches[1] : 1;
            $perPage = 5;
            $offset = ($page - 1) * $perPage;

            $totalUsers = \App\Models\User::count();
            $totalBalance = \App\Models\User::sum('tien');
            $activeUsers = \App\Models\User::where('tien', '>', 0)->count();
            $pendingFeedback = \App\Models\Feedback::where('status', 0)->count();

            $users = \App\Models\User::orderBy('id', 'desc')
                ->offset($offset)
                ->limit($perPage)
                ->get();

            $text = "📊 <b>THỐNG KÊ HỆ THỐNG</b>\n";
            $text .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
            $text .= "📈 <b>TỔNG QUAN</b>\n";
            $text .= "👥 Tổng TK: <b>{$totalUsers}</b>\n";
            $text .= "💰 Tổng dư: <b>" . number_format($totalBalance, 0, ',', '.') . " VNĐ</b>\n";
            $text .= "✅ TK có dư: <b>{$activeUsers}</b>\n";
            $text .= "📋 Feedback chờ: <b>{$pendingFeedback}</b>\n\n";
            $text .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
            $text .= "👤 <b>CHI TIẾT TÀI KHOẢN</b> (Trang {$page}/" . ceil($totalUsers / $perPage) . ")\n";
            $text .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

            if ($users->isEmpty()) {
                $text .= "Không có tài khoản nào.";
            } else {
                foreach ($users as $user) {
                    $text .= "🆔 <b>ID:</b> {$user->id}\n";
                    $text .= "👤 <b>TK:</b> <code>{$user->taikhoan}</code>\n";
                    $text .= "📧 <b>Email:</b> <code>{$user->email}</code>\n";
                    $text .= "💰 <b>Số dư:</b> <b>" . number_format($user->tien, 0, ',', '.') . " VNĐ</b>\n";
                    $text .= "⏰ <b>Ngày tạo:</b> {$user->time}\n";
                    $text .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
                }
            }

            $keyboard = ['inline_keyboard' => []];
            
            // Nút phân trang
            if ($page > 1) {
                $keyboard['inline_keyboard'][] = [['text' => '⬅️ Trước', 'callback_data' => 'user_stats_page_' . ($page - 1)]];
            }
            if ($page < ceil($totalUsers / $perPage)) {
                $keyboard['inline_keyboard'][] = [['text' => 'Tiếp ➡️', 'callback_data' => 'user_stats_page_' . ($page + 1)]];
            }
            
            $keyboard['inline_keyboard'][] = [
                ['text' => '🔄 Làm mới', 'callback_data' => 'menu_user_stats'],
                ['text' => '🏠 Menu', 'callback_data' => 'menu_back']
            ];

            $messageId = $message['message_id'] ?? null;
            if ($messageId) {
                $this->telegramService->editMessageText($chatId, $messageId, $text, 'HTML', $keyboard);
            } else {
                $this->telegramService->sendMessage($chatId, $text, 'HTML', $keyboard);
            }
            if ($callbackQueryId) {
                $this->showSuccess($callbackQueryId, 'Đã tải thống kê');
            }
        } catch (\Exception $e) {
            Log::error('Error handling user stats', ['error' => $e->getMessage()]);
            $this->showError($callbackQueryId, 'Có lỗi xảy ra: ' . $e->getMessage(), true);
        }
    }

    /**
     * Xử lý cộng tiền cho tài khoản - hiển thị danh sách tài khoản
     */
    protected function handleAddBalance(string $chatId, ?string $callbackQueryId, array $message, string $data = 'menu_add_balance'): void
    {
        try {
            // Xử lý phân trang
            if (strpos($data, 'add_balance_page_') === 0) {
                $page = (int)str_replace('add_balance_page_', '', $data);
                $perPage = 50;
                $offset = ($page - 1) * $perPage;

                $totalUsers = \App\Models\User::count();
                $users = \App\Models\User::orderBy('id', 'desc')
                    ->offset($offset)
                    ->limit($perPage)
                    ->get();

                $text = "💰 <b>CỘNG TIỀN CHO TÀI KHOẢN</b>\n";
                $text .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
                $text .= "📊 Tổng số tài khoản: <b>{$totalUsers}</b>\n";
                $text .= "📄 Trang: <b>{$page}/" . ceil($totalUsers / $perPage) . "</b>\n\n";
                $text .= "Chọn tài khoản muốn cộng tiền:\n\n";

                $keyboard = ['inline_keyboard' => []];
                foreach ($users as $user) {
                    $balance = number_format($user->tien, 0, ',', '.');
                    $keyboard['inline_keyboard'][] = [
                        ['text' => "👤 {$user->taikhoan} (💰 {$balance} VNĐ)", 'callback_data' => 'add_balance_user_' . $user->id]
                    ];
                }
                
                // Nút phân trang
                $paginationRow = [];
                if ($page > 1) {
                    $paginationRow[] = ['text' => '⬅️ Trước', 'callback_data' => 'add_balance_page_' . ($page - 1)];
                }
                if ($page < ceil($totalUsers / $perPage)) {
                    $paginationRow[] = ['text' => 'Tiếp ➡️', 'callback_data' => 'add_balance_page_' . ($page + 1)];
                }
                if (!empty($paginationRow)) {
                    $keyboard['inline_keyboard'][] = $paginationRow;
                }
                
                $keyboard['inline_keyboard'][] = [['text' => '🏠 Menu', 'callback_data' => 'menu_back']];

                $messageId = $message['message_id'] ?? null;
                if ($messageId) {
                    $this->telegramService->editMessageText($chatId, $messageId, $text, 'HTML', $keyboard);
                } else {
                    $this->telegramService->sendMessage($chatId, $text, 'HTML', $keyboard);
                }
                $this->showSuccess($callbackQueryId, 'Đã tải danh sách tài khoản');
                return;
            }
            
            // Nếu click vào user cụ thể
            if (strpos($data, 'add_balance_user_') === 0) {
                $userId = str_replace('add_balance_user_', '', $data);
                $user = \App\Models\User::find($userId);
                if (!$user) {
                    $this->showError($callbackQueryId, 'Không tìm thấy tài khoản', true);
                    return;
                }

                $text = "💰 <b>CỘNG TIỀN CHO TÀI KHOẢN</b>\n";
                $text .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
                $text .= "👤 <b>Tài khoản:</b> <code>{$user->taikhoan}</code>\n";
                $text .= "📧 <b>Email:</b> <code>{$user->email}</code>\n";
                $text .= "💰 <b>Số dư hiện tại:</b> <b>" . number_format($user->tien, 0, ',', '.') . " VNĐ</b>\n\n";
                $text .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
                $text .= "Chọn số tiền muốn cộng:\n\n";

                $keyboard = [
                    'inline_keyboard' => [
                        [
                            ['text' => '➕ 10,000 VNĐ', 'callback_data' => 'add_balance_amount_' . $userId . '_10000'],
                            ['text' => '➕ 50,000 VNĐ', 'callback_data' => 'add_balance_amount_' . $userId . '_50000']
                        ],
                        [
                            ['text' => '➕ 100,000 VNĐ', 'callback_data' => 'add_balance_amount_' . $userId . '_100000'],
                            ['text' => '➕ 500,000 VNĐ', 'callback_data' => 'add_balance_amount_' . $userId . '_500000']
                        ],
                        [
                            ['text' => '➕ 1,000,000 VNĐ', 'callback_data' => 'add_balance_amount_' . $userId . '_1000000']
                        ],
                        [
                            ['text' => '⬅️ Quay lại', 'callback_data' => 'menu_add_balance']
                        ]
                    ]
                ];

                $messageId = $message['message_id'] ?? null;
                if ($messageId) {
                    $this->telegramService->editMessageText($chatId, $messageId, $text, 'HTML', $keyboard);
                } else {
                    $this->telegramService->sendMessage($chatId, $text, 'HTML', $keyboard);
                }
                $this->showSuccess($callbackQueryId, 'Chọn số tiền', false);
                return;
            }

            // Nếu click vào số tiền
            if (strpos($data, 'add_balance_amount_') === 0) {
                $parts = explode('_', $data);
                $userId = $parts[3];
                $amount = (int)$parts[4];
                
                $user = \App\Models\User::find($userId);
                if (!$user) {
                    $this->showError($callbackQueryId, 'Không tìm thấy tài khoản', true);
                    return;
                }

                $oldBalance = $user->tien;
                $user->incrementBalance($amount);
                $newBalance = $user->tien;

                $text = "✅ <b>CỘNG TIỀN THÀNH CÔNG!</b>\n";
                $text .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
                $text .= "👤 <b>Tài khoản:</b> <code>{$user->taikhoan}</code>\n";
                $text .= "💰 <b>Số tiền:</b> <b>" . number_format($amount, 0, ',', '.') . " VNĐ</b>\n";
                $text .= "📊 <b>Số dư cũ:</b> " . number_format($oldBalance, 0, ',', '.') . " VNĐ\n";
                $text .= "📊 <b>Số dư mới:</b> <b>" . number_format($newBalance, 0, ',', '.') . " VNĐ</b>\n";

                $keyboard = [
                    'inline_keyboard' => [
                        [['text' => '🔄 Cộng thêm', 'callback_data' => 'add_balance_user_' . $userId]],
                        [['text' => '🏠 Menu', 'callback_data' => 'menu_back']]
                    ]
                ];

                $messageId = $message['message_id'] ?? null;
                if ($messageId) {
                    $this->telegramService->editMessageText($chatId, $messageId, $text, 'HTML', $keyboard);
                } else {
                    $this->telegramService->sendMessage($chatId, $text, 'HTML', $keyboard);
                }
                $this->showSuccess($callbackQueryId, 'Đã cộng tiền thành công!', false);

                Log::info('Balance added via Telegram menu', [
                    'username' => $user->taikhoan,
                    'amount' => $amount,
                    'admin_chat_id' => $chatId
                ]);
                return;
            }

            // Hiển thị danh sách tài khoản - phân trang nếu quá nhiều
            $page = 1;
            $perPage = 50; // Telegram cho phép tối đa 100 nút, nhưng để 50 để dễ nhìn
            $offset = ($page - 1) * $perPage;

            $totalUsers = \App\Models\User::count();
            $users = \App\Models\User::orderBy('id', 'desc')
                ->offset($offset)
                ->limit($perPage)
                ->get();

            $text = "💰 <b>CỘNG TIỀN CHO TÀI KHOẢN</b>\n";
            $text .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
            $text .= "📊 Tổng số tài khoản: <b>{$totalUsers}</b>\n";
            $text .= "📄 Trang: <b>{$page}/" . ceil($totalUsers / $perPage) . "</b>\n\n";
            $text .= "Chọn tài khoản muốn cộng tiền:\n\n";

            $keyboard = ['inline_keyboard' => []];
            foreach ($users as $user) {
                $balance = number_format($user->tien, 0, ',', '.');
                $keyboard['inline_keyboard'][] = [
                    ['text' => "👤 {$user->taikhoan} (💰 {$balance} VNĐ)", 'callback_data' => 'add_balance_user_' . $user->id]
                ];
            }
            
            // Nút phân trang
            $paginationRow = [];
            if ($page > 1) {
                $paginationRow[] = ['text' => '⬅️ Trước', 'callback_data' => 'add_balance_page_' . ($page - 1)];
            }
            if ($page < ceil($totalUsers / $perPage)) {
                $paginationRow[] = ['text' => 'Tiếp ➡️', 'callback_data' => 'add_balance_page_' . ($page + 1)];
            }
            if (!empty($paginationRow)) {
                $keyboard['inline_keyboard'][] = $paginationRow;
            }
            
            $keyboard['inline_keyboard'][] = [['text' => '🏠 Menu', 'callback_data' => 'menu_back']];

            $messageId = $message['message_id'] ?? null;
            if ($messageId) {
                $this->telegramService->editMessageText($chatId, $messageId, $text, 'HTML', $keyboard);
            } else {
                $this->telegramService->sendMessage($chatId, $text, 'HTML', $keyboard);
            }
            if ($callbackQueryId) {
                $this->showSuccess($callbackQueryId, 'Đã tải danh sách tài khoản');
            }
        } catch (\Exception $e) {
            Log::error('Error handling add balance', ['error' => $e->getMessage()]);
            $this->showError($callbackQueryId, 'Có lỗi xảy ra: ' . $e->getMessage(), true);
        }
    }

    /**
     * Xử lý cập nhật DNS - hiển thị danh sách domain đang yêu cầu
     */
    protected function handleUpdateDNS(string $chatId, ?string $callbackQueryId, array $message, string $data = 'menu_update_dns'): void
    {
        try {
            // Xử lý từ chối yêu cầu DNS
            if (strpos($data, 'reject_dns_') === 0) {
                $domainId = str_replace('reject_dns_', '', $data);
                $history = \App\Models\Order::find($domainId);
                if (!$history) {
                    $this->showError($callbackQueryId, 'Không tìm thấy domain', true);
                    return;
                }

                $history->ahihi = '0';
                $history->status = '4'; // Từ chối
                $history->save();

                $text = "❌ <b>ĐÃ TỪ CHỐI YÊU CẦU CẬP NHẬT DNS</b>\n";
                $text .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
                $text .= "🌍 <b>Domain:</b> <code>" . $history->domain . "</code>\n";
                $text .= "👤 <b>User:</b> <code>" . ($history->user ? $history->user->taikhoan : 'N/A') . "</code>\n";
                $text .= "⏰ <b>Thời gian:</b> " . date('d/m/Y H:i:s') . "\n\n";
                $text .= "✅ Yêu cầu đã được từ chối.";

                $keyboard = [
                    'inline_keyboard' => [
                        [['text' => '🔄 Xem danh sách', 'callback_data' => 'menu_update_dns']],
                        [['text' => '🏠 Menu', 'callback_data' => 'menu_back']]
                    ]
                ];

                $messageId = $message['message_id'] ?? null;
                if ($messageId) {
                    $this->telegramService->editMessageText($chatId, $messageId, $text, 'HTML', $keyboard);
                } else {
                    $this->telegramService->sendMessage($chatId, $text, 'HTML', $keyboard);
                }
                $this->showSuccess($callbackQueryId, 'Đã từ chối yêu cầu', false);

                Log::info('DNS request rejected via Telegram', [
                    'domain_id' => $domainId,
                    'admin_chat_id' => $chatId
                ]);
                return;
            }

            // Xử lý cập nhật DNS với NS cụ thể
            if (strpos($data, 'dns_update_') === 0) {
                // Format: dns_update_{domainId}_{ns1}_{ns2}
                $parts = explode('_', $data, 5);
                if (count($parts) >= 5) {
                    $domainId = $parts[2];
                    $ns1 = urldecode($parts[3]);
                    $ns2 = urldecode($parts[4]);
                    
                    $history = \App\Models\Order::find($domainId);
                    if (!$history) {
                        $this->showError($callbackQueryId, 'Không tìm thấy domain', true);
                        return;
                    }

                    $oldNs1 = $history->ns1;
                    $oldNs2 = $history->ns2;

                    $history->ns1 = $ns1;
                    $history->ns2 = $ns2;
                    $history->ahihi = '0';
                    $history->status = '1'; // Đã duyệt
                    $history->save();

                    $text = "✅ <b>CẬP NHẬT DNS THÀNH CÔNG!</b>\n";
                    $text .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
                    $text .= "🌍 <b>Domain:</b> <code>" . $history->domain . "</code>\n";
                    $text .= "👤 <b>User:</b> <code>" . ($history->user ? $history->user->taikhoan : 'N/A') . "</code>\n\n";
                    $text .= "📊 <b>NS1 cũ:</b> <code>" . $oldNs1 . "</code>\n";
                    $text .= "📊 <b>NS1 mới:</b> <code>" . $ns1 . "</code>\n";
                    $text .= "📊 <b>NS2 cũ:</b> <code>" . $oldNs2 . "</code>\n";
                    $text .= "📊 <b>NS2 mới:</b> <code>" . $ns2 . "</code>\n\n";
                    $text .= "⏰ DNS sẽ có hiệu lực sau 12-24h";

                    $keyboard = [
                        'inline_keyboard' => [
                            [['text' => '🔄 Xem danh sách', 'callback_data' => 'menu_update_dns']],
                            [['text' => '🏠 Menu', 'callback_data' => 'menu_back']]
                        ]
                    ];

                    $messageId = $message['message_id'] ?? null;
                    if ($messageId) {
                        $this->telegramService->editMessageText($chatId, $messageId, $text, 'HTML', $keyboard);
                    } else {
                        $this->telegramService->sendMessage($chatId, $text, 'HTML', $keyboard);
                    }
                    $this->showSuccess($callbackQueryId, 'Đã cập nhật DNS thành công!', false);

                    Log::info('DNS updated via Telegram', [
                        'domain_id' => $domainId,
                        'ns1' => $ns1,
                        'ns2' => $ns2,
                        'admin_chat_id' => $chatId
                    ]);
                    return;
                }
            }

            // Nếu click "Nhập tay"
            if (strpos($data, 'dns_manual_') === 0) {
                $domainId = str_replace('dns_manual_', '', $data);
                $history = \App\Models\Order::find($domainId);
                if (!$history) {
                    $this->showError($callbackQueryId, 'Không tìm thấy domain', true);
                    return;
                }

                $text = "📝 <b>NHẬP DNS THỦ CÔNG</b>\n";
                $text .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
                $text .= "🌍 <b>Domain:</b> <code>" . $history->domain . "</code>\n\n";
                $text .= "Nhập DNS mới theo format:\n";
                $text .= "<code>updatedns:" . $history->domain . ":ns1:ns2</code>\n\n";
                $text .= "Ví dụ:\n";
                $text .= "<code>updatedns:" . $history->domain . ":ns1.example.com:ns2.example.com</code>";

                $keyboard = [
                    'inline_keyboard' => [
                        [['text' => '⬅️ Quay lại', 'callback_data' => 'update_dns_' . $domainId]]
                    ]
                ];

                $messageId = $message['message_id'] ?? null;
                if ($messageId) {
                    $this->telegramService->editMessageText($chatId, $messageId, $text, 'HTML', $keyboard);
                } else {
                    $this->telegramService->sendMessage($chatId, $text, 'HTML', $keyboard);
                }
                $this->showSuccess($callbackQueryId, 'Nhập DNS mới', false);
                return;
            }

            // Nếu click vào domain cụ thể để cập nhật
            if (strpos($data, 'update_dns_') === 0) {
                $domainId = str_replace('update_dns_', '', $data);
                $history = \App\Models\Order::find($domainId);
                if (!$history) {
                    $this->showError($callbackQueryId, 'Không tìm thấy domain', true);
                    return;
                }

                $username = $history->user ? $history->user->taikhoan : 'N/A';
                $domain = $history->domain;
                
                $text = "🌐 <b>CẬP NHẬT DNS</b>\n";
                $text .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
                $text .= "🌍 <b>Domain:</b> <code>" . $domain . "</code>\n";
                $text .= "👤 <b>User:</b> <code>" . $username . "</code>\n";
                $text .= "📊 <b>NS1 hiện tại:</b> <code>" . $history->ns1 . "</code>\n";
                $text .= "📊 <b>NS2 hiện tại:</b> <code>" . $history->ns2 . "</code>\n\n";
                $text .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
                $text .= "Chọn DNS mới hoặc nhập tay:\n\n";
                $text .= "Nhập theo format:\n";
                $text .= "<code>updatedns:" . $domain . ":ns1:ns2</code>";

                // Tạo keyboard với các NS phổ biến
                $keyboard = [
                    'inline_keyboard' => [
                        [
                            ['text' => '🌐 Cloudflare', 'callback_data' => 'dns_update_' . $domainId . '_' . urlencode('ns1.cloudflare.com') . '_' . urlencode('ns2.cloudflare.com')]
                        ],
                        [
                            ['text' => '☁️ Namecheap', 'callback_data' => 'dns_update_' . $domainId . '_' . urlencode('dns1.registrar-servers.com') . '_' . urlencode('dns2.registrar-servers.com')]
                        ],
                        [
                            ['text' => '📝 Nhập tay', 'callback_data' => 'dns_manual_' . $domainId]
                        ],
                        [
                            ['text' => '⬅️ Quay lại', 'callback_data' => 'menu_update_dns']
                        ]
                    ]
                ];

                $messageId = $message['message_id'] ?? null;
                if ($messageId) {
                    $this->telegramService->editMessageText($chatId, $messageId, $text, 'HTML', $keyboard);
                } else {
                    $this->telegramService->sendMessage($chatId, $text, 'HTML', $keyboard);
                }
                $this->showSuccess($callbackQueryId, 'Chọn DNS mới', false);
                return;
            }

            // Hiển thị danh sách domain đang yêu cầu cập nhật DNS (ahihi = 1)
            $domains = \App\Models\Order::where('product_type', 'domain')
                ->where('options->ahihi', '1')
                ->orderBy('id', 'desc')
                ->limit(20)
                ->get();

            $text = "🌐 <b>CẬP NHẬT DNS</b>\n";
            $text .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

            if ($domains->isEmpty()) {
                $text .= "✅ <b>KHÔNG CÓ ĐƠN NÀO YÊU CẦU CẬP NHẬT DNS</b>\n\n";
                $text .= "Tất cả đơn hàng đã được xử lý!";
            } else {
                $text .= "📋 <b>DANH SÁCH DOMAIN ĐANG YÊU CẦU</b> (" . $domains->count() . ")\n";
                $text .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

                $keyboard = ['inline_keyboard' => []];
                foreach ($domains as $domain) {
                    $username = $domain->user ? $domain->user->taikhoan : 'N/A';
                    $text .= "🌍 <b>Domain:</b> <code>{$domain->domain}</code>\n";
                    $text .= "👤 <b>User:</b> <code>{$username}</code>\n";
                    $text .= "📊 <b>NS1:</b> <code>{$domain->ns1}</code>\n";
                    $text .= "📊 <b>NS2:</b> <code>{$domain->ns2}</code>\n";
                    $text .= "⏰ <b>Thời gian:</b> {$domain->time}\n";
                    $text .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

                    // Thêm 2 nút: Cập nhật và Từ chối
                    $keyboard['inline_keyboard'][] = [
                        ['text' => '✅ Cập nhật', 'callback_data' => 'update_dns_' . $domain->id],
                        ['text' => '❌ Từ chối', 'callback_data' => 'reject_dns_' . $domain->id]
                    ];
                }
                $keyboard['inline_keyboard'][] = [['text' => '🏠 Menu', 'callback_data' => 'menu_back']];
            }

            if (!isset($keyboard)) {
                $keyboard = [
                    'inline_keyboard' => [
                        [['text' => '🔄 Làm mới', 'callback_data' => 'menu_update_dns']],
                        [['text' => '🏠 Menu', 'callback_data' => 'menu_back']]
                    ]
                ];
            }

            $messageId = $message['message_id'] ?? null;
            if ($messageId) {
                $this->telegramService->editMessageText($chatId, $messageId, $text, 'HTML', $keyboard);
            } else {
                $this->telegramService->sendMessage($chatId, $text, 'HTML', $keyboard);
            }
            if ($callbackQueryId) {
                $this->showSuccess($callbackQueryId, 'Đã tải danh sách');
            }
        } catch (\Exception $e) {
            Log::error('Error handling update DNS', ['error' => $e->getMessage()]);
            $this->showError($callbackQueryId, 'Có lỗi xảy ra: ' . $e->getMessage(), true);
        }
    }

    /**
     * Xử lý xem đơn hàng mới - hiển thị trực quan
     */
    protected function handleNewOrders(string $chatId, ?string $callbackQueryId, array $message): void
    {
        try {
            // Đơn hàng mới (status = 0 - Chờ xử lý)
            $newOrders = \App\Models\Order::where('status', 0)
                ->orderBy('id', 'desc')
                ->limit(10)
                ->get();

            // Đơn hàng đã duyệt (status = 1)
            $approvedOrders = \App\Models\Order::where('status', 1)
                ->orderBy('id', 'desc')
                ->limit(5)
                ->get();

            $text = "📦 <b>QUẢN LÝ ĐƠN HÀNG</b>\n";
            $text .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

            // Thống kê
            $totalNew = \App\Models\Order::where('status', 0)->count();
            $totalApproved = \App\Models\Order::where('status', 1)->count();
            $totalCancelled = \App\Models\Order::where('status', 2)->count();

            $text .= "📊 <b>THỐNG KÊ</b>\n";
            $text .= "⏳ Chờ xử lý: <b>{$totalNew}</b>\n";
            $text .= "✅ Đã duyệt: <b>{$totalApproved}</b>\n";
            $text .= "❌ Đã hủy: <b>{$totalCancelled}</b>\n\n";
            $text .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

            if ($newOrders->isEmpty()) {
                $text .= "✅ <b>KHÔNG CÓ ĐƠN HÀNG MỚI</b>\n\n";
                $text .= "Tất cả đơn hàng đã được xử lý!";
            } else {
                $text .= "⏳ <b>ĐƠN HÀNG CHỜ XỬ LÝ</b> (" . $newOrders->count() . ")\n";
                $text .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

                foreach ($newOrders as $order) {
                    $username = $order->user->taikhoan ?? 'N/A';
                    $text .= "🆔 <b>ID:</b> {$order->id}\n";
                    $text .= "🌍 <b>Domain:</b> <code>{$order->domain}</code>\n";
                    $text .= "👤 <b>User:</b> <code>{$username}</code>\n";
                    $text .= "🔖 <b>MGD:</b> <code>{$order->mgd}</code>\n";
                    $text .= "📊 <b>NS1:</b> <code>{$order->ns1}</code>\n";
                    $text .= "📊 <b>NS2:</b> <code>{$order->ns2}</code>\n";
                    $text .= "⏰ <b>Thời gian:</b> {$order->time}\n";
                    $text .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
                }
            }

            $keyboard = [
                'inline_keyboard' => [
                    [['text' => '🔄 Làm mới', 'callback_data' => 'menu_new_orders']],
                    [['text' => '🏠 Menu', 'callback_data' => 'menu_back']]
                ]
            ];

            $messageId = $message['message_id'] ?? null;
            if ($messageId) {
                $this->telegramService->editMessageText($chatId, $messageId, $text, 'HTML', $keyboard);
            } else {
                $this->telegramService->sendMessage($chatId, $text, 'HTML', $keyboard);
            }
            if ($callbackQueryId) {
                $this->showSuccess($callbackQueryId, 'Đã tải danh sách đơn hàng');
            }
        } catch (\Exception $e) {
            Log::error('Error handling new orders', ['error' => $e->getMessage()]);
            $this->showError($callbackQueryId, 'Có lỗi xảy ra: ' . $e->getMessage(), true);
        }
    }

    /**
     * Xử lý lệnh cộng tiền: congtien:username:amount
     */
    protected function processAddBalance(string $chatId, string $username, string $amount): void
    {
        try {
            $user = \App\Models\User::findByUsername($username);
            if (!$user) {
                $this->telegramService->sendMessage($chatId, "❌ Không tìm thấy tài khoản: <code>{$username}</code>", 'HTML');
                return;
            }

            $amountInt = (int)$amount;
            if ($amountInt <= 0) {
                $this->telegramService->sendMessage($chatId, "❌ Số tiền phải lớn hơn 0!", 'HTML');
                return;
            }

            $oldBalance = $user->tien;
            $user->incrementBalance($amountInt);
            $newBalance = $user->tien;

            $text = "✅ <b>CỘNG TIỀN THÀNH CÔNG</b>\n\n";
            $text .= "👤 Tài khoản: <code>{$username}</code>\n";
            $text .= "💰 Số tiền: <b>" . number_format($amountInt, 0, ',', '.') . " VNĐ</b>\n";
            $text .= "📊 Số dư cũ: <b>" . number_format($oldBalance, 0, ',', '.') . " VNĐ</b>\n";
            $text .= "📊 Số dư mới: <b>" . number_format($newBalance, 0, ',', '.') . " VNĐ</b>";

            $keyboard = [
                'inline_keyboard' => [
                    [['text' => '🏠 Về menu chính', 'callback_data' => 'menu_back']]
                ]
            ];

            $this->telegramService->sendMessage($chatId, $text, 'HTML', $keyboard);
            
            Log::info('Balance added via Telegram', [
                'username' => $username,
                'amount' => $amountInt,
                'admin_chat_id' => $chatId
            ]);
        } catch (\Exception $e) {
            Log::error('Error processing add balance', ['error' => $e->getMessage()]);
            $this->telegramService->sendMessage($chatId, "❌ Có lỗi xảy ra: " . $e->getMessage());
        }
    }

    /**
     * Xử lý lệnh cập nhật DNS: updatedns:domain:ns1:ns2
     */
    protected function processUpdateDNS(string $chatId, string $domain, string $ns1, string $ns2): void
    {
        try {
            $history = \App\Models\Order::where('product_type', 'domain')->where('options->domain', $domain)->first();
            if (!$history) {
                $this->telegramService->sendMessage($chatId, "❌ Không tìm thấy domain: <code>{$domain}</code>", 'HTML');
                return;
            }

            $oldNs1 = $history->ns1;
            $oldNs2 = $history->ns2;

            $history->ns1 = $ns1;
            $history->ns2 = $ns2;
            $history->ahihi = 0; // Đánh dấu đã cập nhật
            $history->save();

            $text = "✅ <b>CẬP NHẬT DNS THÀNH CÔNG</b>\n\n";
            $text .= "🌐 Domain: <code>{$domain}</code>\n";
            $text .= "📊 NS1 cũ: <code>{$oldNs1}</code>\n";
            $text .= "📊 NS1 mới: <code>{$ns1}</code>\n";
            $text .= "📊 NS2 cũ: <code>{$oldNs2}</code>\n";
            $text .= "📊 NS2 mới: <code>{$ns2}</code>\n\n";
            $text .= "⏰ DNS sẽ có hiệu lực sau 12-24h";

            $keyboard = [
                'inline_keyboard' => [
                    [['text' => '🏠 Về menu chính', 'callback_data' => 'menu_back']]
                ]
            ];

            $this->telegramService->sendMessage($chatId, $text, 'HTML', $keyboard);
            
            Log::info('DNS updated via Telegram', [
                'domain' => $domain,
                'ns1' => $ns1,
                'ns2' => $ns2,
                'admin_chat_id' => $chatId
            ]);
        } catch (\Exception $e) {
            Log::error('Error processing update DNS', ['error' => $e->getMessage()]);
            $this->telegramService->sendMessage($chatId, "❌ Có lỗi xảy ra: " . $e->getMessage());
        }
    }

    /**
     * Xử lý lệnh gửi phản hồi: reply:feedbackId:nội dung
     */
    protected function processReplyFeedback(string $chatId, string $feedbackId, string $replyText): void
    {
        try {
            $feedback = \App\Models\Feedback::find($feedbackId);
            if (!$feedback) {
                $this->telegramService->sendMessage($chatId, "❌ Không tìm thấy feedback ID: <code>{$feedbackId}</code>", 'HTML');
                return;
            }

            $replyTime = date('d/m/Y - H:i:s');
            $feedback->admin_reply = $replyText;
            $feedback->reply_time = $replyTime;
            $feedback->status = 1;
            $feedback->save();

            // Gửi tin nhắn cho user qua Telegram nếu có chat ID
            $telegramSent = false;
            if (!empty($feedback->telegram_chat_id)) {
                $telegramMessage = "✅ <b>PHẢN HỒI TỪ ADMIN</b>\n\n";
                $telegramMessage .= $replyText . "\n\n";
                $telegramMessage .= "⏰ " . $replyTime;
                $telegramSent = $this->telegramService->sendMessage($feedback->telegram_chat_id, $telegramMessage, 'HTML');
            }

            $text = "✅ <b>ĐÃ GỬI PHẢN HỒI THÀNH CÔNG!</b>\n\n";
            $text .= "🆔 <b>Feedback ID:</b> #{$feedbackId}\n";
            $text .= "👤 <b>User:</b> <code>{$feedback->username}</code>\n";
            $text .= "📧 <b>Email:</b> <code>{$feedback->email}</code>\n\n";
            $text .= "📝 <b>Phản hồi:</b>\n{$replyText}\n\n";
            $text .= "⏰ <b>Thời gian:</b> {$replyTime}\n";
            if ($telegramSent) {
                $text .= "\n✅ Tin nhắn đã được gửi qua Telegram cho user.";
            } elseif (!empty($feedback->telegram_chat_id)) {
                $text .= "\n⚠️ Không thể gửi tin nhắn qua Telegram (có thể user đã chặn bot).";
            }

            $keyboard = [
                'inline_keyboard' => [
                    [['text' => '🔄 Xem danh sách', 'callback_data' => 'menu_pending_feedback']],
                    [['text' => '🏠 Menu', 'callback_data' => 'menu_back']]
                ]
            ];

            $this->telegramService->sendMessage($chatId, $text, 'HTML', $keyboard);
            
            Log::info('Feedback reply sent via Telegram', [
                'feedback_id' => $feedbackId,
                'admin_chat_id' => $chatId,
                'telegram_sent' => $telegramSent
            ]);
        } catch (\Exception $e) {
            Log::error('Error processing reply feedback', ['error' => $e->getMessage()]);
            $this->telegramService->sendMessage($chatId, "❌ Có lỗi xảy ra: " . $e->getMessage());
        }
    }

    /**
     * Helper method để thêm nút Menu vào keyboard
     * 
     * @param array $keyboard - Keyboard hiện tại
     * @return array - Keyboard đã thêm nút Menu
     */
    protected function addMenuButton(array $keyboard): array
    {
        // Đảm bảo có nút Menu ở cuối
        $hasMenuButton = false;
        if (isset($keyboard['inline_keyboard'])) {
            foreach ($keyboard['inline_keyboard'] as $row) {
                foreach ($row as $button) {
                    if (isset($button['callback_data']) && $button['callback_data'] === 'menu_back') {
                        $hasMenuButton = true;
                        break 2;
                    }
                }
            }
        }
        
        if (!$hasMenuButton) {
            if (!isset($keyboard['inline_keyboard'])) {
                $keyboard['inline_keyboard'] = [];
            }
            $keyboard['inline_keyboard'][] = [['text' => '🏠 MENU', 'callback_data' => 'menu_back']];
        }
        
        return $keyboard;
    }

    /**
     * Helper method để hiển thị loading indicator
     * 
     * @param string|null $callbackQueryId - ID của callback query
     * @param string $message - Thông báo loading (mặc định: "Đang xử lý...")
     * @return void
     */
    protected function showLoading(?string $callbackQueryId, string $message = '⏳ Đang xử lý...'): void
    {
        if ($callbackQueryId) {
            // Hiển thị loading indicator (notification)
            $this->telegramService->answerCallbackQuery($callbackQueryId, $message, false);
        }
    }

    /**
     * Helper method để hiển thị lỗi
     * 
     * @param string|null $callbackQueryId - ID của callback query
     * @param string $errorMessage - Thông báo lỗi
     * @param bool $showAlert - Hiển thị alert popup (mặc định: true)
     * @return void
     */
    protected function showError(?string $callbackQueryId, string $errorMessage, bool $showAlert = true): void
    {
        if ($callbackQueryId) {
            $this->telegramService->answerCallbackQuery($callbackQueryId, '❌ ' . $errorMessage, $showAlert);
        }
    }

    /**
     * Helper method để hiển thị thành công
     * 
     * @param string|null $callbackQueryId - ID của callback query
     * @param string $successMessage - Thông báo thành công
     * @param bool $showAlert - Hiển thị alert popup (mặc định: false)
     * @return void
     */
    protected function showSuccess(?string $callbackQueryId, string $successMessage, bool $showAlert = false): void
    {
        if ($callbackQueryId) {
            $this->telegramService->answerCallbackQuery($callbackQueryId, '✅ ' . $successMessage, $showAlert);
        }
    }
    /**
     * Quản lý Voucher từ Telegram
     */
    protected function handleVoucherManagement(string $chatId): void
    {
        try {
            $total = \App\Models\Voucher::count();
            $used  = \App\Models\Voucher::where('is_used', 1)->count();
            $unused = $total - $used;

            $text = "🎁 <b>QUẢN LÝ VOUCHER</b>\n";
            $text .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
            $text .= "📊 Tổng số mã: <b>{$total}</b>\n";
            $text .= "✅ Đã sử dụng: <b>{$used}</b>\n";
            $text .= "⏳ Chưa sử dụng: <b>{$unused}</b>\n\n";
            $text .= "Sếp có thể tạo mã mới hoặc xem danh sách trên Web.";

            $keyboard = [
                'inline_keyboard' => [
                    [['text' => '🔄 Làm mới', 'callback_data' => 'menu_voucher_stats']],
                    [['text' => '🏠 Menu chính', 'callback_data' => 'menu_back']]
                ]
            ];

            $this->telegramService->sendMessage($chatId, $text, 'HTML', $keyboard);
        } catch (\Exception $e) {
            $this->telegramService->sendMessage($chatId, "❌ Lỗi: " . $e->getMessage());
        }
    }

    /**
     * Menu Cài đặt hệ thống
     */
    protected function handleSettingsMenu(string $chatId): void
    {
        try {
            $settings = \App\Models\Settings::getOne();
            $mMode = ($settings->maintenance_mode ?? 0) == 1 ? "🔴 ĐANG BẢO TRÌ" : "🟢 HOẠT ĐỘNG";

            $text = "🛠️ <b>CÀI ĐẶT HỆ THỐNG</b>\n";
            $text .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
            $text .= "🛡️ Trạng thái: <b>{$mMode}</b>\n";
            $text .= "📢 Thông báo: <code>" . ($settings->thongbao ?? 'Trống') . "</code>\n";
            $text .= "🤖 n8n Chatbot: " . ($settings->n8n_chatbot_url ? "✅" : "❌") . "\n";
            $text .= "🤖 n8n Security: " . ($settings->n8n_security_url ? "✅" : "❌") . "\n\n";
            $text .= "Sếp muốn thực hiện thao tác nào?";

            $keyboard = [
                'inline_keyboard' => [
                    [
                        ['text' => ($settings->maintenance_mode ? '🔓 Mở Web' : '🔒 Đóng Web (Bảo trì)'), 'callback_data' => 'toggle_maintenance']
                    ],
                    [
                        ['text' => '📢 Cập nhật thông báo', 'callback_data' => 'edit_broadcast']
                    ],
                    [
                        ['text' => '🏠 Menu chính', 'callback_data' => 'menu_back']
                    ]
                ]
            ];

            $this->telegramService->sendMessage($chatId, $text, 'HTML', $keyboard);
        } catch (\Exception $e) {
            $this->telegramService->sendMessage($chatId, "❌ Lỗi: " . $e->getMessage());
        }
    }
    /**
     * Xử lý Bật/Tắt chế độ bảo trì
     */
    protected function handleToggleMaintenance(string $chatId, ?string $callbackQueryId, array $message): void
    {
        try {
            $settings = \App\Models\Settings::getOne();
            $settings->maintenance_mode = ($settings->maintenance_mode == 1 ? 0 : 1);
            $settings->save();

            $status = $settings->maintenance_mode == 1 ? "🔴 ĐÃ BẬT BẢO TRÌ" : "🟢 ĐÃ TẮT BẢO TRÌ (WEB HOẠT ĐỘNG)";
            $this->showSuccess($callbackQueryId, $status, true);

            // Cập nhập lại menu settings
            $this->handleSettingsMenu($chatId);
        } catch (\Exception $e) {
            $this->showError($callbackQueryId, 'Lỗi: ' . $e->getMessage());
        }
    }

    /**
     * Hướng dẫn cập nhật thông báo
     */
    protected function handleEditBroadcast(string $chatId): void
    {
        $text = "📢 <b>CẬP NHẬT THÔNG BÁO HỆ THỐNG</b>\n";
        $text .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
        $text .= "Sếp vui lòng nhập nội dung theo cú pháp:\n";
        $text .= "<code>broadcast:Nội dung thông báo mới</code>\n\n";
        $text .= "Ví dụ:\n";
        $text .= "<code>broadcast:Chào mừng bạn đến với VTKT. Chúc bạn một ngày tốt lành!</code>";

        $this->telegramService->sendMessage($chatId, $text, 'HTML', [
            'inline_keyboard' => [[['text' => '⬅️ Quay lại', 'callback_data' => 'menu_settings']]]
        ]);
    }

    /**
     * Thực hiện cập nhật thông báo vào DB
     */
    protected function processUpdateBroadcast(string $chatId, string $content): void
    {
        try {
            $settings = \App\Models\Settings::getOne();
            $settings->thongbao = trim($content);
            $settings->save();

            $this->telegramService->sendMessage($chatId, "✅ <b>Cập nhật thông báo thành công!</b>\n\n📝 Nội dung: <i>{$content}</i>", 'HTML', [
                'inline_keyboard' => [[['text' => '🏠 Về Menu', 'callback_data' => 'menu_back']]]
            ]);
        } catch (\Exception $e) {
            $this->telegramService->sendMessage($chatId, "❌ Lỗi: " . $e->getMessage());
        }
    }

    /**
     * Xử lý Duyệt/Hủy đơn hàng trực tiếp
     */
    protected function processOrderAction(string $chatId, int $orderId, int $status): void
    {
        try {
            $order = \App\Models\Order::find($orderId);
            if (!$order) {
                $this->telegramService->sendMessage($chatId, "❌ Không tìm thấy đơn hàng ID: #{$orderId}");
                return;
            }

            if ($order->status != 0 && $order->status != 4) { // Chờ duyệt hoặc đang xử lý
                $this->telegramService->sendMessage($chatId, "⚠️ Đơn hàng này đã được xử lý trước đó (Status: {$order->status})");
                return;
            }

            $order->status = $status;
            $order->save();

            $statusText = ($status == 1 ? "✅ ĐÃ DUYỆT" : "❌ ĐÃ HỦY");
            $text = "{$statusText} <b>ĐƠN HÀNG #{$orderId}</b>\n";
            $text .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
            $text .= "👤 Khách: <code>" . ($order->user->taikhoan ?? 'Unknow') . "</code>\n";
            $text .= "📦 Loại: <b>" . strtoupper($order->product_type) . "</b>\n";
            $text .= "🔖 Mã GD: <code>{$order->mgd}</code>\n";
            $text .= "⏰ Xử lý: " . date('H:i:s d/m/Y');

            $this->telegramService->sendMessage($chatId, $text, 'HTML', [
                'inline_keyboard' => [[['text' => '📦 Xem đơn khác', 'callback_data' => 'menu_new_orders']]]
            ]);

            Log::info("Order #{$orderId} processed via Telegram by Admin", ['status' => $status]);
        } catch (\Exception $e) {
            $this->telegramService->sendMessage($chatId, "❌ Lỗi khi xử lý đơn: " . $e->getMessage());
        }
    }
}

