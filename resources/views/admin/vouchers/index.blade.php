@extends('layouts.admin')

@section('title', 'Quản Lý Voucher')

@section('content')
<div class="col-span-12 mt-6">
    <div class="intro-y block sm:flex items-center h-10">
        <h2 class="text-lg font-medium truncate mr-5">Danh Sách Voucher</h2>
        <div class="flex items-center sm:ml-auto mt-3 sm:mt-0">
            <a href="{{ route('admin.vouchers.create') }}" class="btn btn-primary shadow-md mr-2">Tạo Voucher Mới</a>
        </div>
    </div>
    
    <div class="intro-y box mt-5">
        <div class="p-5">
            @if(session('success'))
                <script>swal("Thành công", "{{ session('success') }}", "success");</script>
            @endif

            <div class="overflow-x-auto">
                <table class="table">
                    <thead style="background-color: #1e293b !important;">
                        <tr>
                            <th class="whitespace-nowrap" style="color: #ffffff !important;">Mã Code</th>
                            <th class="whitespace-nowrap" style="color: #ffffff !important;">Giá Trị</th>
                            <th class="whitespace-nowrap" style="color: #ffffff !important;">Chủ Sở Hữu</th>
                            <th class="whitespace-nowrap" style="color: #ffffff !important;">Trạng Thái</th>
                            <th class="whitespace-nowrap" style="color: #ffffff !important;">Ngày Hết Hạn</th>
                            <th class="whitespace-nowrap" style="color: #ffffff !important;">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($vouchers->count() > 0)
                            @foreach($vouchers as $voucher)
                            <tr>
                                <td class="font-bold text-primary">{{ $voucher->code }}</td>
                                <td>{{ number_format($voucher->value) }}đ</td>
                                <td>
                                    @if($voucher->user_id)
                                        <span class="text-info">{{ $voucher->user->taikhoan }}</span>
                                    @else
                                        <span class="badge badge-light-success">Toàn sàn (Global)</span>
                                    @endif
                                </td>
                                <td>
                                    @if($voucher->is_used)
                                        <span class="text-danger">Đã sử dụng</span>
                                    @elseif($voucher->expires_at->isPast())
                                        <span class="text-gray-500">Hết hạn</span>
                                    @else
                                        <span class="text-success font-medium">Sẵn sàng</span>
                                    @endif
                                </td>
                                <td>{{ $voucher->expires_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <form action="{{ route('admin.vouchers.destroy', $voucher->id) }}" method="POST" onsubmit="return confirm('Sếp có chắc muốn xóa mã này không?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="text-center py-5">Chưa có voucher nào được tạo</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $vouchers->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
