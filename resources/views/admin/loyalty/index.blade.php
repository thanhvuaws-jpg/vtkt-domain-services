@extends('layouts.admin')

@section('title', 'Thưởng & Top Spender')

@section('content')
<div class="grid grid-cols-12 gap-6 mt-5">
    <!-- Top Spender Dashboard -->
    <div class="col-span-12 lg:col-span-12">
        <div class="intro-y block sm:flex items-center h-10">
            <h2 class="text-lg font-medium truncate mr-5">Biểu Đồ Đại Gia Tháng {{ now()->month }}</h2>
        </div>
        <div class="intro-y box p-5 mt-5">
            <div class="overflow-x-auto">
                <table class="table table-report mt-2">
                    <thead style="background-color: #1e293b !important;">
                        <tr>
                            <th class="whitespace-nowrap" style="color: #ffffff !important;">Hạng</th>
                            <th class="whitespace-nowrap" style="color: #ffffff !important;">Tài Khoản</th>
                            <th class="whitespace-nowrap text-center" style="color: #ffffff !important;">Tổng Chi Tiêu (Tháng này)</th>
                            <th class="whitespace-nowrap text-center" style="color: #ffffff !important;">Quà Dự Kiến</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($topSpenders->isNotEmpty())
                            @foreach($topSpenders as $index => $rank)
                            <tr class="intro-x">
                                <td class="w-10">
                                    <div class="flex items-center justify-center font-bold {{ $index < 3 ? 'text-warning' : '' }}">
                                        #{{ $index + 1 }}
                                    </div>
                                </td>
                                <td>
                                    <span class="font-medium whitespace-nowrap">{{ $rank->taikhoan }}</span>
                                    <div class="text-slate-500 text-xs whitespace-nowrap mt-0.5">UID: {{ $rank->id }}</div>
                                </td>
                                <td class="text-center font-bold text-success">{{ number_format($rank->total_spent) }}đ</td>
                                <td class="text-center">
                                    @if($index == 0)
                                        <span class="badge badge-light-warning">Voucher 500k 👑</span>
                                    @elseif($index == 1)
                                        <span class="badge badge-light-warning">Voucher 300k 🥈</span>
                                    @elseif($index == 2)
                                        <span class="badge badge-light-warning">Voucher 100k 🥉</span>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr><td colspan="4" class="text-center py-5">Chưa có dữ liệu chi tiêu tháng này</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="mt-5 p-4 bg-primary bg-opacity-10 rounded border border-dashed border-primary">
                <p class="text-primary fs-8 mb-0">💡 Lưu ý: Hệ thống hiện đang cấu hình Top 3 thắng cuộc sẽ nhận Voucher vào Kho Quà thủ công sau khi kết thúc tháng.</p>
            </div>
        </div>
    </div>

    <!-- History of Top Rewards -->
    <div class="col-span-12 mt-10">
        <div class="intro-y block sm:flex items-center h-10">
            <h2 class="text-lg font-medium truncate mr-5">Lịch Sử Nhận Thưởng Top (Tháng Trước)</h2>
        </div>
        <div class="intro-y box p-5 mt-5">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead style="background-color: #1e293b !important;">
                        <tr>
                            <th class="whitespace-nowrap" style="color: #ffffff !important;">Nội Dung Giải</th>
                            <th class="whitespace-nowrap" style="color: #ffffff !important;">Người Nhận</th>
                            <th class="whitespace-nowrap" style="color: #ffffff !important;">Giá Trị Quà</th>
                            <th class="whitespace-nowrap" style="color: #ffffff !important;">Mã Voucher</th>
                            <th class="whitespace-nowrap" style="color: #ffffff !important;">Thời Gian Nhận</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($topVouchers->count() > 0)
                            @foreach($topVouchers as $tv)
                            <tr>
                                <td>{{ $tv->mota }}</td>
                                <td>{{ $tv->user->taikhoan }}</td>
                                <td class="font-bold text-success">{{ number_format($tv->value) }}đ</td>
                                <td class="text-primary font-bold">{{ $tv->code }}</td>
                                <td>{{ $tv->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="text-center py-10">Chưa có lịch sử nhận thưởng nào được ghi nhận</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $topVouchers->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
