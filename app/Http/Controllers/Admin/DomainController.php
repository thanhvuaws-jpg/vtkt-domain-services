<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Domain;
use App\Traits\HasAvailableImages;
use Illuminate\Http\Request;

class DomainController extends Controller
{
    use HasAvailableImages;

    /**
     * Hiển thị danh sách domain
     * Lấy tất cả loại domain và hiển thị trên trang admin
     * 
     * @return \Illuminate\View\View - View danh sách domain
     */
    public function index()
    {
        // Lấy tất cả domain từ database, sắp xếp theo ID giảm dần (mới nhất trước)
        $domains = Domain::orderBy('id', 'desc')->get();
        // Trả về view với dữ liệu domains
        return view('admin.domain.index', compact('domains'));
    }

    /**
     * Hiển thị form thêm domain mới
     * Lấy danh sách ảnh có sẵn trong thư mục images để admin chọn
     * 
     * @return \Illuminate\View\View - View form thêm domain
     */
    public function create()
    {
        $availableImages = $this->getAvailableImages('domain');
        return view('admin.domain.create', compact('availableImages'));
    }

    /**
     * Lưu domain mới vào database
     * 
     * @param Request $request - HTTP request chứa duoi, price, image
     * @return \Illuminate\Http\RedirectResponse - Redirect về danh sách domain với thông báo
     */
    public function store(Request $request)
    {
        // Validate dữ liệu đầu vào từ form
        $request->validate([
            'duoi' => 'required', // Đuôi domain bắt buộc
            'price' => 'required|integer', // Giá bắt buộc, phải là số nguyên
            'image' => 'required', // Ảnh bắt buộc
        ]);

        // Chuyển đổi đường dẫn ảnh về định dạng storage (images/filename.jpg)
        $imagePath = $request->image;
        // Nếu đường dẫn có chứa '/images/', chỉ lấy tên file
        if ($imagePath && strpos($imagePath, '/images/') !== false) {
            $imagePath = 'images/' . basename($imagePath);
        }

        // Tạo domain mới trong database
        Domain::create([
            'duoi' => $request->duoi, // Đuôi domain
            'price' => (int)$request->price, // Giá (ép kiểu về int)
            'image' => $imagePath, // Đường dẫn ảnh
        ]);

        // Redirect về danh sách domain với thông báo thành công
        return redirect()->route('admin.domain.index')
            ->with('success', 'Thêm thành công!');
    }

    /**
     * Hiển thị form chỉnh sửa domain
     * 
     * @param int $id - ID của domain cần chỉnh sửa
     * @return \Illuminate\View\View - View form chỉnh sửa
     */
    public function edit($id)
    {
        $domain          = Domain::findOrFail($id);
        $availableImages = $this->getAvailableImages('domain');
        return view('admin.domain.edit', compact('domain', 'availableImages'));
    }

    /**
     * Cập nhật domain trong database
     * 
     * @param Request $request - HTTP request chứa duoi, price, image
     * @param int $id - ID của domain cần cập nhật
     * @return \Illuminate\Http\RedirectResponse - Redirect về danh sách domain với thông báo
     */
    public function update(Request $request, $id)
    {
        // Validate dữ liệu đầu vào từ form
        $request->validate([
            'duoi' => 'required', // Đuôi domain bắt buộc
            'price' => 'required|integer', // Giá bắt buộc, phải là số nguyên
            'image' => 'required', // Ảnh bắt buộc
        ]);

        // Tìm domain theo ID, nếu không tìm thấy thì throw 404
        $domain = Domain::findOrFail($id);
        
        // Chuyển đổi đường dẫn ảnh về định dạng storage (images/filename.jpg)
        $imagePath = $request->image;
        // Nếu đường dẫn có chứa '/images/', chỉ lấy tên file
        if ($imagePath && strpos($imagePath, '/images/') !== false) {
            $imagePath = 'images/' . basename($imagePath);
        }
        
        // Cập nhật domain trong database
        $domain->update([
            'duoi' => $request->duoi, // Đuôi domain
            'price' => (int)$request->price, // Giá (ép kiểu về int)
            'image' => $imagePath, // Đường dẫn ảnh
        ]);

        // Redirect về danh sách domain với thông báo thành công
        return redirect()->route('admin.domain.index')
            ->with('success', 'Cập nhật thành công!');
    }

    /**
     * Xóa domain khỏi database
     * 
     * @param int $id - ID của domain cần xóa
     * @return \Illuminate\Http\RedirectResponse - Redirect về danh sách domain với thông báo
     */
    public function destroy($id)
    {
        // Tìm domain theo ID, nếu không tìm thấy thì throw 404
        $domain = Domain::findOrFail($id);
        // Xóa domain khỏi database
        $domain->delete();

        // Redirect về danh sách domain với thông báo thành công
        return redirect()->route('admin.domain.index')
            ->with('success', 'Xóa thành công!');
    }
}

