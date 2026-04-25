@extends('layouts.app')

@section('content')
<div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">
    <div class="app-container container-xxl d-flex flex-row flex-column-fluid">
        <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
            <div class="d-flex flex-column flex-column-fluid">
                <div id="kt_app_content" class="app-content flex-column-fluid py-6">

                    {{-- ====== HEADER TỔNG QUAN ====== --}}
                    <div class="d-flex align-items-center justify-content-between mb-6 flex-wrap gap-3">
                        <div>
                            <h2 class="fw-bold mb-1" style="font-size:1.5rem;">📦 Quản Lý Dịch Vụ</h2>
                            <span class="text-muted fs-7">Tổng hợp toàn bộ đơn hàng của bạn</span>
                        </div>
                        <a href="{{ route('home') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i> Đặt Thêm Dịch Vụ
                        </a>
                    </div>

                    {{-- ====== THỐNG KÊ NHANH ====== --}}
                    <div class="row g-3 mb-6">
                        <div class="col-6 col-md-3">
                            <div class="card text-center py-4" style="border-left: 4px solid #6366f1;">
                                <div class="fw-bold fs-2 text-primary">{{ $domainOrders->count() }}</div>
                                <div class="text-muted fs-8 mt-1">🌐 Domain</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card text-center py-4" style="border-left: 4px solid #10b981;">
                                <div class="fw-bold fs-2" style="color:#10b981;">{{ $hostingOrders->count() }}</div>
                                <div class="text-muted fs-8 mt-1">🖥️ Hosting</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card text-center py-4" style="border-left: 4px solid #f59e0b;">
                                <div class="fw-bold fs-2" style="color:#f59e0b;">{{ $vpsOrders->count() }}</div>
                                <div class="text-muted fs-8 mt-1">⚡ VPS</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card text-center py-4" style="border-left: 4px solid #ec4899;">
                                <div class="fw-bold fs-2" style="color:#ec4899;">{{ $sourceCodeOrders->count() }}</div>
                                <div class="text-muted fs-8 mt-1">💻 Source Code</div>
                            </div>
                        </div>
                    </div>

                    {{-- ====== HỆ THỐNG TAB ====== --}}
                    <div class="card">
                        <div class="card-header border-0 pt-5 pb-0">
                            <ul class="nav nav-tabs nav-line-tabs nav-line-tabs-2x border-0 fw-semibold fs-6" id="serviceTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active d-flex align-items-center gap-2" data-bs-toggle="tab" href="#tab-domain" role="tab">
                                        🌐 <span>Domain</span>
                                        <span class="badge badge-light-primary rounded-pill ms-1">{{ $domainOrders->count() }}</span>
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link d-flex align-items-center gap-2" data-bs-toggle="tab" href="#tab-hosting" role="tab">
                                        🖥️ <span>Hosting</span>
                                        <span class="badge badge-light-success rounded-pill ms-1">{{ $hostingOrders->count() }}</span>
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link d-flex align-items-center gap-2" data-bs-toggle="tab" href="#tab-vps" role="tab">
                                        ⚡ <span>VPS</span>
                                        <span class="badge badge-light-warning rounded-pill ms-1">{{ $vpsOrders->count() }}</span>
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link d-flex align-items-center gap-2" data-bs-toggle="tab" href="#tab-sourcecode" role="tab">
                                        💻 <span class="d-none d-sm-inline">Source Code</span><span class="d-inline d-sm-none">Code</span>
                                        <span class="badge badge-light-danger rounded-pill ms-1">{{ $sourceCodeOrders->count() }}</span>
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="card-body tab-content pt-4">

                            {{-- ===== TAB DOMAIN ===== --}}
                            <div class="tab-pane fade show active" id="tab-domain" role="tabpanel">
                                @forelse($domainOrders as $order)
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 py-3 border-bottom">
                                    {{-- Icon + Tên --}}
                                    <div class="d-flex align-items-center gap-3 flex-grow-1" style="min-width:160px;">
                                        <div class="symbol symbol-40px symbol-circle" style="background:rgba(99,102,241,0.1); display:flex; align-items:center; justify-content:center; width:40px; height:40px; border-radius:50%;">
                                            <span style="font-size:1.2rem;">🌐</span>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-gray-800 fs-6">{{ $order->domain }}</div>
                                            <div class="text-muted fs-8">#{{ $order->mgd }} · {{ $order->hsd }} Năm</div>
                                        </div>
                                    </div>
                                    {{-- Status --}}
                                    <div>
                                        @if($order->status == 0)
                                            <span class="badge badge-light-warning px-3 py-2">⏳ Pending</span>
                                        @elseif($order->status == 1)
                                            <span class="badge badge-light-primary px-3 py-2">✅ Active</span>
                                        @elseif($order->status == 2)
                                            <span class="badge badge-light-danger px-3 py-2">🔒 Locked</span>
                                        @elseif($order->status == 3)
                                            <span class="badge badge-light-info px-3 py-2">🔄 Update DNS</span>
                                        @elseif($order->status == 4)
                                            <span class="badge badge-light-danger px-3 py-2">❌ Từ Chối</span>
                                        @endif
                                    </div>
                                    {{-- Thời gian + Quản lý --}}
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="text-muted fs-8 d-none d-md-inline">{{ $order->time }}</span>
                                        <a href="{{ route('manager.domain', $order->id) }}" class="btn btn-sm btn-icon btn-light-primary" title="Quản lý">
                                            <i class="fas fa-arrow-right fs-7"></i>
                                        </a>
                                    </div>
                                </div>
                                @empty
                                <div class="text-center py-10">
                                    <div style="font-size:3rem;">🌐</div>
                                    <p class="text-muted mt-3">Chưa có đơn tên miền nào</p>
                                    <a href="{{ route('home') }}" class="btn btn-primary btn-sm mt-2">Đặt Ngay</a>
                                </div>
                                @endforelse
                            </div>

                            {{-- ===== TAB HOSTING ===== --}}
                            <div class="tab-pane fade" id="tab-hosting" role="tabpanel">
                                @forelse($hostingOrders as $order)
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 py-3 border-bottom">
                                    <div class="d-flex align-items-center gap-3 flex-grow-1" style="min-width:160px;">
                                        <div style="background:rgba(16,185,129,0.1); display:flex; align-items:center; justify-content:center; width:40px; height:40px; border-radius:50%; flex-shrink:0;">
                                            <span style="font-size:1.2rem;">🖥️</span>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-gray-800 fs-6">{{ $order->hosting ? $order->hosting->name : 'N/A' }}</div>
                                            <div class="text-muted fs-8">#{{ $order->mgd }} · {{ $order->period }} Tháng</div>
                                        </div>
                                    </div>
                                    <div>
                                        @if($order->status == 0)
                                            <span class="badge badge-light-warning px-3 py-2">⏳ Pending</span>
                                        @elseif($order->status == 1)
                                            <span class="badge badge-light-primary px-3 py-2">✅ Active</span>
                                        @elseif($order->status == 2)
                                            <span class="badge badge-light-danger px-3 py-2">❌ Rejected</span>
                                        @endif
                                    </div>
                                    <span class="text-muted fs-8 d-none d-md-inline">{{ $order->time }}</span>
                                    
                                    {{-- Info Panel cho Hosting --}}
                                    @if(!empty($order->options['username']))
                                    <div class="w-100 mt-2 p-3 bg-light rounded border border-secondary border-dashed" style="font-size:0.9rem;">
                                        <div class="d-flex flex-wrap gap-4">
                                            <div><span class="text-muted">Server:</span> <strong class="text-primary">{{ $order->options['ip'] ?? '---' }}</strong></div>
                                            <div><span class="text-muted">User:</span> <strong>{{ $order->options['username'] }}</strong></div>
                                            <div><span class="text-muted">Pass:</span> <span class="user-select-all bg-white px-2 py-1 rounded border">{{ $order->options['password'] }}</span></div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                @empty
                                <div class="text-center py-10">
                                    <div style="font-size:3rem;">🖥️</div>
                                    <p class="text-muted mt-3">Chưa có đơn hosting nào</p>
                                    <a href="{{ route('hosting.index') }}" class="btn btn-primary btn-sm mt-2">Đặt Ngay</a>
                                </div>
                                @endforelse
                            </div>

                            {{-- ===== TAB VPS ===== --}}
                            <div class="tab-pane fade" id="tab-vps" role="tabpanel">
                                @forelse($vpsOrders as $order)
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 py-3 border-bottom">
                                    <div class="d-flex align-items-center gap-3 flex-grow-1" style="min-width:160px;">
                                        <div style="background:rgba(245,158,11,0.1); display:flex; align-items:center; justify-content:center; width:40px; height:40px; border-radius:50%; flex-shrink:0;">
                                            <span style="font-size:1.2rem;">⚡</span>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-gray-800 fs-6">{{ $order->vps ? $order->vps->name : 'N/A' }}</div>
                                            <div class="text-muted fs-8">#{{ $order->mgd }} · {{ $order->period }} Tháng</div>
                                        </div>
                                    </div>
                                    <div>
                                        @if($order->status == 0)
                                            <span class="badge badge-light-warning px-3 py-2">⏳ Pending</span>
                                        @elseif($order->status == 1)
                                            <span class="badge badge-light-primary px-3 py-2">✅ Active</span>
                                        @elseif($order->status == 2)
                                            <span class="badge badge-light-danger px-3 py-2">❌ Rejected</span>
                                        @endif
                                    </div>
                                    <span class="text-muted fs-8 d-none d-md-inline">{{ $order->time }}</span>
                                    
                                    {{-- Info Panel cho VPS --}}
                                    @if(!empty($order->options['username']))
                                    <div class="w-100 mt-2 p-3 bg-light rounded border border-secondary border-dashed" style="font-size:0.9rem;">
                                        <div class="d-flex flex-wrap gap-4">
                                            <div><span class="text-muted">IP:</span> <strong class="text-primary">{{ $order->options['ip'] ?? '---' }}</strong></div>
                                            <div><span class="text-muted">User:</span> <strong>{{ $order->options['username'] }}</strong></div>
                                            <div><span class="text-muted">Pass:</span> <span class="user-select-all bg-white px-2 py-1 rounded border">{{ $order->options['password'] }}</span></div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                @empty
                                <div class="text-center py-10">
                                    <div style="font-size:3rem;">⚡</div>
                                    <p class="text-muted mt-3">Chưa có đơn VPS nào</p>
                                    <a href="{{ route('vps.index') }}" class="btn btn-primary btn-sm mt-2">Đặt Ngay</a>
                                </div>
                                @endforelse
                            </div>

                            {{-- ===== TAB SOURCE CODE ===== --}}
                            <div class="tab-pane fade" id="tab-sourcecode" role="tabpanel">
                                @forelse($sourceCodeOrders as $order)
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 py-3 border-bottom">
                                    <div class="d-flex align-items-center gap-3 flex-grow-1" style="min-width:160px;">
                                        <div style="background:rgba(236,72,153,0.1); display:flex; align-items:center; justify-content:center; width:40px; height:40px; border-radius:50%; flex-shrink:0;">
                                            <span style="font-size:1.2rem;">💻</span>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-gray-800 fs-6">{{ $order->sourceCode ? $order->sourceCode->name : 'N/A' }}</div>
                                            <div class="text-muted fs-8">#{{ $order->mgd }}</div>
                                        </div>
                                    </div>
                                    <div>
                                        @if($order->status == 0)
                                            <span class="badge badge-light-warning px-3 py-2">⏳ Pending</span>
                                        @elseif($order->status == 1)
                                            <span class="badge badge-light-primary px-3 py-2">✅ Active</span>
                                        @elseif($order->status == 2)
                                            <span class="badge badge-light-danger px-3 py-2">❌ Rejected</span>
                                        @endif
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="text-muted fs-8 d-none d-md-inline">{{ $order->time }}</span>
                                        @if($order->status == 1)
                                            @if($order->sourceCode && (!empty($order->sourceCode->file_path) || !empty($order->sourceCode->download_link)))
                                                @if(!empty($order->sourceCode->file_path))
                                                    <a href="{{ route('download.file', $order->id) }}" class="btn btn-sm btn-success">
                                                        <i class="fas fa-download me-1"></i>Download
                                                    </a>
                                                @elseif(!empty($order->sourceCode->download_link))
                                                    <a href="{{ $order->sourceCode->download_link }}" class="btn btn-sm btn-success" target="_blank">
                                                        <i class="fas fa-external-link-alt me-1"></i>Download
                                                    </a>
                                                @endif
                                            @else
                                                <span class="btn btn-sm btn-secondary disabled">Liên hệ Admin</span>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                                @empty
                                <div class="text-center py-10">
                                    <div style="font-size:3rem;">💻</div>
                                    <p class="text-muted mt-3">Chưa có đơn Source Code nào</p>
                                    <a href="{{ route('source-code.index') }}" class="btn btn-primary btn-sm mt-2">Đặt Ngay</a>
                                </div>
                                @endforelse
                            </div>

                        </div>{{-- end tab-content --}}
                    </div>{{-- end card --}}

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
