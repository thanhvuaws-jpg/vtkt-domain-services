<?php
// Khai báo namespace cho Controller này - thuộc App\Http\Controllers
namespace App\Http\Controllers;

// Import các Model cần thiết
use App\Models\SourceCode; // Model quản lý source code
use App\Models\Order; // Model lưu lịch sử mua
use App\Models\User; // Model quản lý người dùng
use App\Models\Settings; // Model quản lý cài đặt hệ thống
use Illuminate\Http\Request; // Class xử lý HTTP request
use Illuminate\Support\Facades\Storage; // Facade để thao tác với file storage

/**
 * Class DownloadController
 * Controller xử lý việc tải xuống source code đã mua
 */
class DownloadController extends Controller
{
    /**
     * Hiển thị trang download với danh sách source code đã mua
     * 
     * @param Request $request - HTTP request có thể chứa mgd (mã giao dịch) trong query
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        // Lấy username từ session
        $username = session('users');
        // Tìm user trong database theo username
        $user = User::where('taikhoan', $username)->first();
        
        // Nếu không tìm thấy user (chưa đăng nhập), redirect đến trang đăng nhập
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Lấy ID user và mã giao dịch từ request (nếu có)
        $userId = $user->id; // ID người dùng
        $mgd = $request->get('mgd'); // Mã giao dịch (optional)
        
        // Khởi tạo biến để lưu thông tin đơn hàng cụ thể (nếu có mgd)
        $history = null; // Lịch sử mua hàng cụ thể
        $sourceCode = null; // Source code cụ thể
        
        // Nếu có mã giao dịch (mgd), lấy thông tin đơn hàng cụ thể
        if ($mgd) {
            $history = Order::where('mgd', $mgd)
                ->where('user_id', $userId)
                ->where('product_type', 'sourcecode')
                ->first();
            
            if ($history) {
                $sourceCode = $history->product();
            }
        }
        
        // Lấy tất cả các đơn hàng source code của user này
        $purchases = Order::where('user_id', $userId)
            ->where('product_type', 'sourcecode')
            ->orderBy('id', 'desc')
            ->get();
        
        // Lấy thông tin cài đặt hệ thống (để hiển thị thông tin liên hệ admin)
        $settings = Settings::getOne();
        
        // Trả về view download với tất cả dữ liệu cần thiết
        return view('pages.download', compact('history', 'sourceCode', 'purchases', 'user', 'settings'));
    }
    
    /**
     * Tải xuống file source code
     * Kiểm tra quyền sở hữu và cung cấp file download
     * 
     * @param int $id - ID của đơn hàng (SourceCodeHistory ID)
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|\Illuminate\Http\RedirectResponse
     */
    public function download($id)
    {
        // Lấy username từ session
        $username = session('users');
        // Tìm user trong database theo username
        $user = User::where('taikhoan', $username)->first();
        
        // Nếu không tìm thấy user (chưa đăng nhập), redirect đến trang đăng nhập
        if (!$user) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để tải file');
        }
        
        // Tìm đơn hàng theo ID và ID user (đảm bảo user chỉ tải file của mình)
        $history = Order::where('id', $id)
            ->where('user_id', $user->id)
            ->first();
        
        // Nếu không tìm thấy đơn hàng, redirect với thông báo lỗi
        if (!$history) {
            return redirect()->route('download.index')->with('error', 'Không tìm thấy lịch sử mua hàng');
        }
        
        // Lấy thông tin source code từ đơn hàng
        $sourceCode = $history->product();
        
        // Nếu không tìm thấy source code, redirect với thông báo lỗi
        if (!$sourceCode) {
            return redirect()->route('download.index')->with('error', 'Không tìm thấy source code');
        }
        
        // Kiểm tra file path có tồn tại không
        if (empty($sourceCode->file_path)) {
            return redirect()->route('download.index')->with('error', 'File không tồn tại. Vui lòng liên hệ admin');
        }
        
        // Đường dẫn file tương đối với storage/app/public (ví dụ: 'source-code/filename.zip')
        $filePath = $sourceCode->file_path;
        
        // Kiểm tra file có tồn tại trong storage không
        if (!Storage::disk('public')->exists($filePath)) {
            return redirect()->route('download.index')->with('error', 'File không tồn tại. Vui lòng liên hệ admin');
        }
        
        // Tải xuống file (stream download)
        // basename() lấy tên file từ đường dẫn (ví dụ: 'filename.zip' từ 'source-code/filename.zip')
        return Storage::disk('public')->download($filePath, basename($sourceCode->file_path));
    }
}
