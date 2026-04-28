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
</script>
