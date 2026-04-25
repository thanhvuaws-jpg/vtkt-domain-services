@extends('layouts.admin')

@section('title', 'Tạo Voucher Mới')

@section('content')
<div class="intro-y flex items-center mt-8">
    <h2 class="text-lg font-medium mr-auto">Tạo Voucher Mới</h2>
</div>

<div class="grid grid-cols-12 gap-6 mt-5">
    <div class="intro-y col-span-12 lg:col-span-8">
        <div class="intro-y box p-5">
            <form action="{{ route('admin.vouchers.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="form-label font-bold">Mã Voucher <span class="text-danger">*</span></label>
                    <div class="flex gap-2">
                        <input type="text" name="code" id="voucher_code" class="form-control" placeholder="VD: GIAMGIA10K" required>
                        <button type="button" class="btn btn-secondary" onclick="generateRandomCode()">Ngẫu nhiên</button>
                    </div>
                    <small class="text-muted">Mã sẽ tự động viết hoa khi lưu.</small>
                </div>

                <div class="mb-4">
                    <label class="form-label font-bold">Giá Trị (VNĐ) <span class="text-danger">*</span></label>
                    <input type="number" name="value" class="form-control" placeholder="VD: 10000" required>
                </div>

                <div class="mb-4">
                    <label class="form-label font-bold">Người Sở Hữu (Tùy chọn)</label>
                    <select name="user_id" class="form-control">
                        <option value="">-- Toàn sàn (Dùng chung cho mọi người) --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->taikhoan }} (ID: {{ $user->id }})</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Nếu bỏ trống, bất kỳ ai cũng có thể sử dụng mã này (mỗi người 1 lần).</small>
                </div>

                <div class="mb-4">
                    <label class="form-label font-bold">Ngày Hết Hạn <span class="text-danger">*</span></label>
                    <input type="datetime-local" name="expires_at" class="form-control" value="{{ now()->addMonth()->format('Y-m-d\TH:i') }}" required>
                </div>

                <div class="mb-4">
                    <label class="form-label font-bold">Ghi chú / Mô tả</label>
                    <textarea name="mota" class="form-control" rows="3" placeholder="VD: Voucher tri ân khách hàng..."></textarea>
                </div>

                <div class="text-right mt-5">
                    <a href="{{ route('admin.vouchers.index') }}" class="btn btn-outline-secondary w-24 mr-1">Hủy</a>
                    <button type="submit" class="btn btn-primary w-24">Lưu lại</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function generateRandomCode() {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    let result = 'OFF';
    for (let i = 0; i < 6; i++) {
        result += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    document.getElementById('voucher_code').value = result;
}
</script>
@endsection
