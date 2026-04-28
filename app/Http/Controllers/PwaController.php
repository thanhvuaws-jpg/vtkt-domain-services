<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Settings;

class PwaController extends Controller
{
    /**
     * Trả về cấu hình manifest cho PWA một cách tự động lấy từ Database.
     */
    public function manifest()
    {
        $settings = Settings::getOne();
        $appName = $settings->tieude ?? 'THANHVU.NET';
        $shortName = strlen($appName) > 12 ? substr($appName, 0, 12) : $appName;
        $themeColor = '#181c32'; // Theme mặc định của layout hiện tại

        $manifest = [
            'name' => $appName,
            'short_name' => $shortName,
            'start_url' => '/',
            'display' => 'standalone',
            'background_color' => '#ffffff',
            'theme_color' => $themeColor,
            'description' => $settings->mota ?? 'Hệ thống dịch vụ uy tín chất lượng.',
            'icons' => [
                [
                    'src' => '/images/pwa/web-app-manifest-192x192.png',
                    'sizes' => '192x192',
                    'type' => 'image/png'
                ],
                [
                    'src' => '/images/pwa/web-app-manifest-512x512.png',
                    'sizes' => '512x512',
                    'type' => 'image/png'
                ]
            ]
        ];

        return response()->json($manifest);
    }
}
