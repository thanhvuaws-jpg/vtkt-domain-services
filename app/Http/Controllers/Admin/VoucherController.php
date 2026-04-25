<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VoucherController extends Controller
{
    /**
     * Danh sách Voucher
     */
    public function index()
    {
        $vouchers = Voucher::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('admin.vouchers.index', compact('vouchers'));
    }

    /**
     * Form tạo Voucher
     */
    public function create()
    {
        $users = User::select('id', 'taikhoan')->orderBy('taikhoan')->get();
        return view('admin.vouchers.create', compact('users'));
    }

    /**
     * Lưu Voucher mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:vouchers,code|max:50',
            'value' => 'required|numeric|min:0',
            'expires_at' => 'required|date',
            'user_id' => 'nullable|exists:users,id',
            'mota' => 'nullable|string|max:255'
        ]);

        Voucher::create([
            'code' => strtoupper($request->code),
            'value' => $request->value,
            'user_id' => $request->user_id,
            'expires_at' => $request->expires_at,
            'mota' => $request->mota,
            'is_used' => 0
        ]);

        return redirect()->route('admin.vouchers.index')->with('success', 'Tạo Voucher mới thành công!');
    }

    /**
     * Xóa Voucher
     */
    public function destroy($id)
    {
        $voucher = Voucher::findOrFail($id);
        $voucher->delete();

        return redirect()->route('admin.vouchers.index')->with('success', 'Đã xóa Voucher!');
    }
}
