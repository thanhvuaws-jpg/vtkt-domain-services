<!-- PWA Configuration -->
<link rel="manifest" href="/manifest.json">
<!-- Fallback theme color trong trường hợp db chưa load -->
<meta name="theme-color" content="#181c32">

<!-- Hỗ trợ tốt nhất cho iOS -->
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="{{ isset($settings) ? $settings->tieude : 'THANHVU.NET' }}">
<link rel="apple-touch-icon" href="/images/pwa/apple-touch-icon.png">

<!-- Service Worker Registration -->
<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
            navigator.serviceWorker.register('/sw.js').then(function(registration) {
                console.log('✅ ServiceWorker đăng ký thành công. Web App đã sẵn sàng!');
            }, function(err) {
                console.log('🔴 ServiceWorker đăng ký thất bại: ', err);
            });
        });
    }

    // --- LOGIC HIỂN THỊ NÚT CÀI ĐẶT APP CHỦ ĐỘNG ---
    let deferredPrompt;
    
    // Đón bắt sự kiện PWA sẵn sàng cài đặt (Chặn popup mặc định của trình duyệt)
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
        
        // Hiện nút Cài đặt trên Header
        const installBtnContainer = document.getElementById('installPwaBtnContainer');
        if (installBtnContainer) {
            installBtnContainer.classList.remove('d-none');
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        const installBtn = document.getElementById('installPwaBtn');
        if (installBtn) {
            installBtn.addEventListener('click', async () => {
                if (deferredPrompt) {
                    // Mở popup hệ thống để Cài Đặt
                    deferredPrompt.prompt();
                    // Chờ khách hàng phản hồi
                    const { outcome } = await deferredPrompt.userChoice;
                    if (outcome === 'accepted') {
                        // Ẩn nút đi nếu đã xác nhận cài
                        const installBtnContainer = document.getElementById('installPwaBtnContainer');
                        if (installBtnContainer) installBtnContainer.classList.add('d-none');
                    }
                    deferredPrompt = null;
                }
            });
        }
    });

    // Ẩn nút mãi mãi nếu thiết bị báo là đã cài PWA rồi
    window.addEventListener('appinstalled', () => {
        const installBtnContainer = document.getElementById('installPwaBtnContainer');
        if (installBtnContainer) installBtnContainer.classList.add('d-none');
        deferredPrompt = null;
    });
</script>
