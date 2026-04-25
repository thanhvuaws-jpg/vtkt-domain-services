/**
 * Theme Switcher js
 * Xử lý chức năng chuyển đổi Dark/Light mode
 */
document.addEventListener('DOMContentLoaded', () => {
    // Tìm thẻ html gốc
    const htmlEl = document.documentElement;
    
    // Kiểm tra cache xem người dùng chọn màu gì trước đó chưa
    const savedTheme = localStorage.getItem('vtkt-theme');
    
    // Nếu chưa có, check xem máy tính họ đang dùng Dark Mode hay Light Mode
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    
    // Quyết định Theme lúc mới khởi động
    const defaultTheme = savedTheme ? savedTheme : (prefersDark ? 'dark' : 'light');
    
    // Áp dụng Theme
    htmlEl.setAttribute('data-bs-theme', defaultTheme);
    
    // Cập nhật lại Icon ngay khi load trang
    updateThemeIcon(defaultTheme);

    // Xử lý nút bấm đổi màu (Gắn vào ID btn-theme-toggle)
    const toggleBtn = document.getElementById('btn-theme-toggle');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', () => {
            const currentTheme = htmlEl.getAttribute('data-bs-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            // Xoay màu
            htmlEl.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('vtkt-theme', newTheme);
            
            // Cập nhật Icon
            updateThemeIcon(newTheme);
            
            // Thông báo nho nhỏ (nếu có toastr)
            if (typeof toastr !== 'undefined') {
                const langVi = newTheme === 'dark' ? 'Chế độ Tối' : 'Chế độ Sáng';
                toastr.success(`Đã chuyển sang ${langVi}`, "Cập Nhật Theme");
            }
        });
    }

    function updateThemeIcon(theme) {
        const iconEl = document.getElementById('theme-icon');
        if (iconEl) {
            if (theme === 'dark') {
                iconEl.className = 'fas fa-sun'; // Đang là tối thì icon là Mặt trời để chuyển sang sáng
            } else {
                iconEl.className = 'fas fa-moon'; // Ngược lại
            }
        }
    }
});
