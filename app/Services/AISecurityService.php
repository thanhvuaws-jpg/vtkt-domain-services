<?php

namespace App\Services;

use App\Models\Settings;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AISecurityService
{
    /**
     * Gửi log hành vi người dùng sang n8n Security Observer
     * 
     * @param string $event Tên sự kiện (LOGIN, PURCHASE, CLAIM_GIFT, etc.)
     * @param array $data Dữ liệu chi tiết hành vi
     */
    public function observe($event, array $data = [])
    {
        try {
            $settings = Settings::getOne();
            if (!$settings || empty($settings->n8n_security_url)) {
                return;
            }

            // Chuẩn bị payload chuẩn hóa
            $payload = [
                'timestamp' => now()->format('Y-m-d H:i:s'),
                'event' => strtoupper($event),
                'user_agent' => request()->userAgent(),
                'ip' => request()->ip(),
                'url' => request()->fullUrl(),
                'payload' => $data
            ];

            // Gửi Webhook Async (không block request chính)
            Http::timeout(3)->post($settings->n8n_security_url, $payload);

        } catch (\Exception $e) {
            // Chỉ log warning, không làm crash app nếu n8n tèo
            Log::warning("AISecurityService: Webhook failed: " . $e->getMessage());
        }
    }
}
