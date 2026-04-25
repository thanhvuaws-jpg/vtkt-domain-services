@extends('layouts.admin')

@section('title', 'Dashboard - ADMIN CPANEL')
@section('breadcrumb', 'Dashboard')

@section('content')
<div class="grid grid-cols-12 gap-6">

    {{-- ======= HÀNG 1: KPI CARDS ======= --}}
    <div class="col-span-12 mt-6">
        <div class="intro-y flex items-center h-10 mb-5">
            <h2 class="text-lg font-medium truncate mr-5">📊 Tổng Quan Hệ Thống</h2>
            <a href="" class="ml-auto flex items-center text-primary text-sm">
                <i data-lucide="refresh-ccw" class="w-4 h-4 mr-1"></i> Làm mới
            </a>
        </div>

        <div class="grid grid-cols-12 gap-4">
            {{-- Đơn chờ --}}
            <div class="col-span-6 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in">
                    <div class="box p-5">
                        <div class="flex justify-between">
                            <div class="flex items-center justify-center w-12 h-12 rounded-full" style="background:rgba(99,102,241,0.1);">
                                <i data-lucide="clock" class="w-6 h-6" style="color:#6366f1;"></i>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold">{{ number_format($donhang) }}</div>
                                <div class="text-sm text-slate-400 mt-0.5">Đơn Chờ</div>
                            </div>
                        </div>
                        <div class="mt-3 text-xs text-slate-400 border-t pt-2">⏳ Cần xử lý ngay</div>
                    </div>
                </div>
            </div>

            {{-- Dịch vụ Active --}}
            <div class="col-span-6 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in">
                    <div class="box p-5">
                        <div class="flex justify-between">
                            <div class="flex items-center justify-center w-12 h-12 rounded-full" style="background:rgba(16,185,129,0.1);">
                                <i data-lucide="check-circle" class="w-6 h-6" style="color:#10b981;"></i>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold">{{ number_format($donhoanthanh) }}</div>
                                <div class="text-sm text-slate-400 mt-0.5">Đang Active</div>
                            </div>
                        </div>
                        <div class="mt-3 text-xs text-slate-400 border-t pt-2">✅ Dịch vụ đang chạy</div>
                    </div>
                </div>
            </div>

            {{-- Thành viên --}}
            <div class="col-span-6 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in">
                    <div class="box p-5">
                        <div class="flex justify-between">
                            <div class="flex items-center justify-center w-12 h-12 rounded-full" style="background:rgba(245,158,11,0.1);">
                                <i data-lucide="users" class="w-6 h-6" style="color:#f59e0b;"></i>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold">{{ number_format($thanhvien) }}</div>
                                <div class="text-sm text-slate-400 mt-0.5">Thành Viên</div>
                            </div>
                        </div>
                        <div class="mt-3 text-xs text-slate-400 border-t pt-2">👥 Tổng khách hàng</div>
                    </div>
                </div>
            </div>

            {{-- DNS cần update --}}
            <div class="col-span-6 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in">
                    <div class="box p-5">
                        <div class="flex justify-between">
                            <div class="flex items-center justify-center w-12 h-12 rounded-full" style="background:rgba(239,68,68,0.1);">
                                <i data-lucide="wifi" class="w-6 h-6" style="color:#ef4444;"></i>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold">{{ number_format($update) }}</div>
                                <div class="text-sm text-slate-400 mt-0.5">Cập Nhật DNS</div>
                            </div>
                        </div>
                        <div class="mt-3 text-xs text-slate-400 border-t pt-2">🔄 Đang chờ cập nhật</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ======= HÀNG 2: DOANH THU ======= --}}
    <div class="col-span-12">
        <div class="grid grid-cols-12 gap-4">
            {{-- Doanh thu hôm nay --}}
            <div class="col-span-6 xl:col-span-3 intro-y">
                <div class="box p-5" style="border-left: 3px solid #10b981;">
                    <div class="text-sm text-slate-400">💰 Hôm Nay</div>
                    <div class="text-2xl font-bold mt-1" style="color:#10b981;">{{ number_format($doanhthuhomnay) }}đ</div>
                </div>
            </div>
            {{-- Hôm qua --}}
            <div class="col-span-6 xl:col-span-3 intro-y">
                <div class="box p-5" style="border-left: 3px solid #6366f1;">
                    <div class="text-sm text-slate-400">📅 Hôm Qua</div>
                    <div class="text-2xl font-bold mt-1" style="color:#6366f1;">{{ number_format($doanhthuhqua) }}đ</div>
                </div>
            </div>
            {{-- Tháng này --}}
            <div class="col-span-6 xl:col-span-3 intro-y">
                <div class="box p-5" style="border-left: 3px solid #f59e0b;">
                    <div class="text-sm text-slate-400">📆 Tháng Này</div>
                    <div class="text-2xl font-bold mt-1" style="color:#f59e0b;">{{ number_format($doanhthuthang) }}đ</div>
                </div>
            </div>
            {{-- Tổng --}}
            <div class="col-span-6 xl:col-span-3 intro-y">
                <div class="box p-5" style="border-left: 3px solid #ec4899;">
                    <div class="text-sm text-slate-400">🏆 Tổng Cộng</div>
                    <div class="text-2xl font-bold mt-1" style="color:#ec4899;">{{ number_format($tongdoanhthu) }}đ</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ======= HÀNG 3: BIỂU ĐỒ ======= --}}
    <div class="col-span-12 xl:col-span-7 intro-y">
        <div class="box p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-base">📈 Doanh Thu So Sánh</h3>
                <span class="text-xs text-slate-400">Hôm qua · Hôm nay · Tháng</span>
            </div>
            <div style="position:relative; height:280px;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-span-12 xl:col-span-5 intro-y">
        <div class="box p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-base">🗂️ Phân Bổ Đơn Hàng</h3>
                <span class="text-xs text-slate-400">Theo loại dịch vụ</span>
            </div>
            <div style="position:relative; height:280px;">
                <canvas id="orderTypeChart"></canvas>
            </div>
        </div>
    </div>

    {{-- ======= HÀNG 4: BIỂU ĐỒ TRẠNG THÁI ======= --}}
    <div class="col-span-12 xl:col-span-5 intro-y">
        <div class="box p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-base">🔵 Trạng Thái Đơn Hàng</h3>
            </div>
            <div style="position:relative; height:260px;">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Ghi chú nhanh --}}
    <div class="col-span-12 xl:col-span-7 intro-y">
        <div class="box p-5 h-full">
            <h3 class="font-semibold text-base mb-4">📌 Ghi Chú Nhanh</h3>
            <div class="space-y-3">
                <div class="flex items-center gap-3 p-3 rounded-lg" style="background:rgba(99,102,241,0.06);">
                    <span style="width:10px; height:10px; border-radius:50%; background:#6366f1; flex-shrink:0;"></span>
                    <span class="text-sm">Có <b>{{ $donhang }}</b> đơn hàng đang chờ duyệt — cần xử lý sớm!</span>
                </div>
                <div class="flex items-center gap-3 p-3 rounded-lg" style="background:rgba(239,68,68,0.06);">
                    <span style="width:10px; height:10px; border-radius:50%; background:#ef4444; flex-shrink:0;"></span>
                    <span class="text-sm">Có <b>{{ $update }}</b> domain đang yêu cầu cập nhật DNS.</span>
                </div>
                <div class="flex items-center gap-3 p-3 rounded-lg" style="background:rgba(16,185,129,0.06);">
                    <span style="width:10px; height:10px; border-radius:50%; background:#10b981; flex-shrink:0;"></span>
                    <span class="text-sm">Tổng <b>{{ $donhoanthanh }}</b> dịch vụ đang chạy ổn định.</span>
                </div>
                <div class="flex items-center gap-3 p-3 rounded-lg" style="background:rgba(245,158,11,0.06);">
                    <span style="width:10px; height:10px; border-radius:50%; background:#f59e0b; flex-shrink:0;"></span>
                    <span class="text-sm">Doanh thu tháng này: <b>{{ number_format($doanhthuthang) }}đ</b></span>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// ====== BIỂU ĐỒ DOANH THU (BAR) ======
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
new Chart(revenueCtx, {
    type: 'bar',
    data: {
        labels: ['Hôm Qua', 'Hôm Nay', 'Tháng Này'],
        datasets: [{
            label: 'Doanh Thu (đ)',
            data: [{{ $doanhthuhqua }}, {{ $doanhthuhomnay }}, {{ $doanhthuthang }}],
            backgroundColor: [
                'rgba(99, 102, 241, 0.7)',
                'rgba(16, 185, 129, 0.7)',
                'rgba(245, 158, 11, 0.7)',
            ],
            borderColor: [
                'rgba(99, 102, 241, 1)',
                'rgba(16, 185, 129, 1)',
                'rgba(245, 158, 11, 1)',
            ],
            borderWidth: 2,
            borderRadius: 8,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: (c) => ' ' + Number(c.raw).toLocaleString('vi-VN') + 'đ'
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: (v) => Number(v).toLocaleString('vi-VN') + 'đ'
                },
                grid: { color: 'rgba(0,0,0,0.05)' }
            },
            x: { grid: { display: false } }
        }
    }
});

// ====== BIỂU ĐỒ LOẠI SẢN PHẨM (DOUGHNUT) ======
const typeCtx = document.getElementById('orderTypeChart').getContext('2d');
new Chart(typeCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode(array_keys($orderByType)) !!},
        datasets: [{
            data: {!! json_encode(array_values($orderByType)) !!},
            backgroundColor: [
                'rgba(99, 102, 241, 0.8)',
                'rgba(16, 185, 129, 0.8)',
                'rgba(245, 158, 11, 0.8)',
                'rgba(236, 72, 153, 0.8)',
            ],
            borderWidth: 2,
            borderColor: '#fff',
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '65%',
        plugins: {
            legend: {
                position: 'bottom',
                labels: { padding: 16, usePointStyle: true }
            }
        }
    }
});

// ====== BIỂU ĐỒ TRẠNG THÁI (PIE) ======
const statusCtx = document.getElementById('statusChart').getContext('2d');
new Chart(statusCtx, {
    type: 'pie',
    data: {
        labels: {!! json_encode(array_keys($orderByStatus)) !!},
        datasets: [{
            data: {!! json_encode(array_values($orderByStatus)) !!},
            backgroundColor: [
                'rgba(245, 158, 11, 0.8)',
                'rgba(16, 185, 129, 0.8)',
                'rgba(239, 68, 68, 0.8)',
            ],
            borderWidth: 2,
            borderColor: '#fff',
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: { padding: 16, usePointStyle: true }
            }
        }
    }
});
</script>
@endpush
