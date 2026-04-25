<?php

namespace App\Traits;

/**
 * Trait HasAvailableImages
 * Logic dùng chung để scan folder ảnh trong Admin controllers
 *
 * Thay thế code copy-paste ở 4 Admin controllers:
 * - Admin/DomainController.php (create + edit)
 * - Admin/HostingController.php (create + edit)
 * - Admin/VPSController.php (create + edit)
 * - Admin/SourceCodeController.php (create + edit)
 */
trait HasAvailableImages
{
    /**
     * Lấy danh sách ảnh có sẵn trong một folder
     *
     * @param string $folder Tên folder trong public/images/ (ví dụ: 'domain', 'hosting', 'vps')
     * @return array Danh sách tên file ảnh (không bao gồm '.' và '..')
     */
    protected function getAvailableImages(string $folder): array
    {
        $path = public_path('images/' . $folder);

        // Kiểm tra thư mục có tồn tại không
        if (!is_dir($path)) {
            return [];
        }

        // Lấy danh sách file và lọc bỏ '.' và '..'
        $files = scandir($path);
        if ($files === false) {
            return [];
        }

        // Lọc chỉ lấy file ảnh (không lấy thư mục con)
        return array_values(array_filter($files, function ($file) use ($path) {
            return !in_array($file, ['.', '..']) && is_file($path . '/' . $file);
        }));
    }

    /**
     * Lấy danh sách ảnh kèm đường dẫn URL đầy đủ
     *
     * @param string $folder Tên folder trong public/images/
     * @return array ['filename' => 'example.jpg', 'url' => '/images/hosting/example.jpg']
     */
    protected function getAvailableImagesWithUrl(string $folder): array
    {
        $images = $this->getAvailableImages($folder);

        return array_map(fn($filename) => [
            'filename' => $filename,
            'url'      => asset('images/' . $folder . '/' . $filename),
        ], $images);
    }
}
