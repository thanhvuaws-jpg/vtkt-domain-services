@extends('layouts.app')

@section('content')
<style>
    .gift-container {
        background: linear-gradient(145deg, #1e1e2d 0%, #2b2b40 100%);
        border-radius: 20px;
        position: relative;
        overflow: hidden;
        min-height: 480px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(255,255,255,0.05);
        box-shadow: 0 15px 35px rgba(0,0,0,0.5);
    }
    .gift-box {
        font-size: 110px;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        filter: drop-shadow(0 0 20px rgba(255, 193, 7, 0.3));
    }
    .gift-box:hover {
        transform: scale(1.15) rotate(8deg);
        filter: drop-shadow(0 0 30px rgba(255, 193, 7, 0.6));
    }
    .gift-box.shaking {
        animation: shake 0.5s infinite;
    }
    @keyframes shake {
        0% { transform: translate(2px, 2px) rotate(0deg); }
        10% { transform: translate(-2px, -3px) rotate(-2deg); }
        20% { transform: translate(-4px, 0px) rotate(2deg); }
        30% { transform: translate(4px, 3px) rotate(0deg); }
        40% { transform: translate(2px, -2px) rotate(2deg); }
        50% { transform: translate(-2px, 3px) rotate(-2deg); }
        60% { transform: translate(-4px, 2px) rotate(0deg); }
        70% { transform: translate(4px, 2px) rotate(-2deg); }
        80% { transform: translate(-2px, -2px) rotate(2deg); }
        90% { transform: translate(2px, 3px) rotate(0deg); }
        100% { transform: translate(2px, -3px) rotate(-2deg); }
    }
    .prize-reveal {
        display: none;
        text-align: center;
        animation: bounceIn 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    @keyframes bounceIn {
        0% { opacity: 0; transform: scale(0.3); }
        50% { opacity: 0.9; transform: scale(1.1); }
        80% { opacity: 1; transform: scale(0.89); }
        100% { opacity: 1; transform: scale(1); }
    }
    .rank-card {
        background: rgba(30, 30, 45, 0.6);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.05);
    }
    .rank-item {
        transition: all 0.2s;
        border-radius: 10px;
    }
    .rank-item:hover {
        background: rgba(255,255,255,0.05);
    }
    .rank-badge {
        width: 25px;
        height: 25px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-weight: bold;
    }
    .rank-1 { background: #FFD700; color: #000; box-shadow: 0 0 10px rgba(255, 215, 0, 0.5); }
    .rank-2 { background: #C0C0C0; color: #000; }
    .rank-3 { background: #CD7F32; color: #fff; }
    
    /* Progress milestones */
    .milestone-track {
        position: relative;
        padding-left: 45px;
    }
    .milestone-track::before {
        content: '';
        position: absolute;
        left: 20px;
        top: 0;
        bottom: 0;
        width: 4px;
        background: rgba(255,255,255,0.1);
        border-radius: 2px;
    }
    .milestone-progress {
        position: absolute;
        left: 20px;
        top: 0;
        width: 4px;
        background: #009ef7;
        border-radius: 2px;
        transition: height 1s ease;
    }
    .milestone-item {
        position: relative;
        margin-bottom: 40px;
        padding: 10px 15px;
        background: rgba(255,255,255,0.03);
        border-radius: 10px;
        border: 1px solid rgba(255,255,255,0.05);
    }
    .milestone-item.achieved {
        border-color: rgba(0, 158, 247, 0.3);
        background: rgba(0, 158, 247, 0.05);
    }
    .milestone-dot {
        position: absolute;
        left: -32px;
        top: 50%;
        transform: translateY(-50%);
        width: 14px;
        height: 14px;
        background: #3f4254;
        border-radius: 50%;
        z-index: 2;
        border: 3px solid #1e1e2d;
    }
    .milestone-item.achieved .milestone-dot {
        background: #009ef7;
        box-shadow: 0 0 10px #009ef7;
    }
    .copy-btn { cursor: pointer; transition: 0.2s; }
    .copy-btn:hover { opacity: 0.8; transform: scale(1.05); }

    /* Leaderboard Scroll Animation */
    @keyframes slideUpRank {
        0% { transform: translateY(0); }
        100% { transform: translateY(-50%); }
    }
    .rank-list-slider {
        display: flex;
        flex-direction: column;
    }
    .animate-scroll {
        animation: slideUpRank 20s linear infinite;
    }
    .rank-list-container:hover .animate-scroll {
        animation-play-state: paused;
    }
    
    .hover-primary:hover { color: #009ef7 !important; }
    .table-row-gray-800 tr { border-bottom-color: rgba(255,255,255,0.05) !important; }
</style>

<div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">
    <div class="app-container container-xxl">
        <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
            <div class="d-flex flex-column flex-column-fluid">
                <div id="kt_app_content" class="app-content flex-column-fluid mt-10">
                    @if($canClaimTop)
                    <div class="alert alert-dismissible bg-light-warning d-flex flex-column flex-sm-row p-5 mb-10 border border-warning border-dashed">
                        <i class="fas fa-crown fs-2hx text-warning me-4 mb-5 mb-sm-0"></i>
                        <div class="d-flex flex-column pe-0 pe-sm-10">
                            <h4 class="fw-bold">Chúc mừng sếp! 👑</h4>
                            <span class="fs-6">Sếp đã vinh dự đạt <b>Top {{ $canClaimTop['rank'] }}</b> đại gia của tháng {{ $canClaimTop['month'] }}/{{ $canClaimTop['year'] }}.</span>
                        </div>
                        <button type="button" class="btn btn-warning ms-sm-auto align-self-center mt-3 mt-sm-0 fw-bold px-8 shadow-sm" id="btn_claim_top">
                            NHẬN VOUCHER {{ number_format($canClaimTop['value']) }}đ ✨
                        </button>
                    </div>
                    @endif
                    
                    <div class="row g-6">
                        <!-- LEFT COLUMN: LEADERBOARD -->
                        <div class="col-lg-3">
                            <div class="card rank-card h-100">
                                <div class="card-header border-0 pt-5">
                                    <h3 class="card-title align-items-start flex-column">
                                        <span class="card-label fw-bold text-white fs-3">🏆 Bảng Xếp Hạng</span>
                                        <span class="text-muted mt-1 fw-semibold fs-7">Top đại gia tháng {{ now()->month }}</span>
                                    </h3>
                                </div>
                                <div class="card-body pt-3">
                                    <div class="rank-list-container" style="max-height: 450px; overflow: hidden; position: relative;">
                                        <div class="rank-list-slider {{ count($topSpenders) > 5 ? 'animate-scroll' : '' }}" id="rank_slider">
                                            @empty($topSpenders)
                                                <div class="text-center py-10">
                                                    <p class="text-gray-500">Chưa có dữ liệu tháng này</p>
                                                </div>
                                            @else
                                                {{-- Lượt 1 --}}
                                                @foreach($topSpenders as $index => $rank)
                                                <div class="d-flex align-items-center mb-5 rank-item p-2">
                                                    <div class="symbol symbol-30px me-3">
                                                        <span class="rank-badge rank-{{ $index + 1 }} fs-8">{{ $index + 1 }}</span>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <div class="d-flex align-items-center">
                                                            <span class="text-white fw-bold d-block fs-6 me-2">{{ $rank->taikhoan }}</span>
                                                            @if($index < 3)
                                                                <span class="badge badge-light-warning fs-9 fw-bold">🎁 +{{ $index == 0 ? '500k' : ($index == 1 ? '300k' : '100k') }}</span>
                                                            @endif
                                                        </div>
                                                        <span class="text-muted fw-semibold fs-8">Sếp hạng {{ $index + 1 }}</span>
                                                    </div>
                                                    <div class="text-end">
                                                        <span class="text-success fw-bold fs-7">{{ number_format($rank->total_spent) }}đ</span>
                                                    </div>
                                                </div>
                                                @endforeach

                                                {{-- Lượt 2 để tạo hiệu ứng cuộn vô tận --}}
                                                @if(count($topSpenders) > 5)
                                                    @foreach($topSpenders as $index => $rank)
                                                    <div class="d-flex align-items-center mb-5 rank-item p-2">
                                                        <div class="symbol symbol-30px me-3">
                                                            <span class="rank-badge rank-{{ $index + 1 }} fs-8">{{ $index + 1 }}</span>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <div class="d-flex align-items-center">
                                                                <span class="text-white fw-bold d-block fs-6 me-2">{{ $rank->taikhoan }}</span>
                                                                @if($index < 3)
                                                                    <span class="badge badge-light-warning fs-9 fw-bold">🎁 +{{ $index == 0 ? '500k' : ($index == 1 ? '300k' : '100k') }}</span>
                                                                @endif
                                                            </div>
                                                            <span class="text-muted fw-semibold fs-8">Sếp hạng {{ $index + 1 }}</span>
                                                        </div>
                                                        <div class="text-end">
                                                            <span class="text-success fw-bold fs-7">{{ number_format($rank->total_spent) }}đ</span>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                @endif
                                            @endempty
                                        </div>
                                    </div>
                                    
                                    <div class="mt-5 p-4 bg-primary bg-opacity-10 rounded border border-primary border-dashed">
                                        <p class="text-primary fs-8 mb-0">🎁 Nhận Voucher cho Top 1, 2, 3 vào cuối tháng!</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- CENTER COLUMN: GIFT, WALLET & GLOBAL -->
                        <div class="col-lg-6">
                            <!-- Gift Box Section -->
                            <div class="gift-container p-10 mb-6">
                                @if ($user->lucky_draw_played)
                                    <div class="text-center prize-reveal" style="display: block;">
                                        <i class="fas fa-check-circle text-success mb-5" style="font-size: 70px;"></i>
                                        <h2 class="text-white mb-3">Sếp đã nhận quà rồi!</h2>
                                        <p class="text-gray-400 mb-8 fs-6">Mã bốc thăm của sếp đã được hệ thống ghi nhận.</p>
                                        <a href="{{ route('home') }}" class="btn btn-outline btn-outline-dashed btn-outline-primary btn-lg px-8">Khám Phá Dịch Vụ 🚀</a>
                                    </div>
                                @else
                                    <div id="draw_area" class="text-center">
                                        <span class="badge badge-light-danger fw-bold fs-8 mb-4 px-4 py-2 border border-danger border-dashed">NEW MEMBER GIFT 🎁</span>
                                        <div class="gift-box mb-6" id="gift_box">🎁</div>
                                        <h2 class="text-white fw-bold mb-8">Bốc Thăm May Mắn!</h2>
                                        <button class="btn btn-danger btn-lg fs-3 px-15 py-4 fw-bold shadow-lg" id="btn_draw">
                                            <span class="indicator-label">MỞ QUÀ NGAY ✨</span>
                                        </button>
                                    </div>

                                    <div class="prize-reveal" id="prize_result">
                                        <i class="fas fa-trophy text-warning mb-5" style="font-size: 80px;"></i>
                                        <h2 class="text-white fs-1 mb-2">XUẤT SẮC!</h2>
                                        <h3 class="text-warning fs-2hx fw-bold mb-5" id="prize_value">Voucher 50,000đ</h3>
                                        <div class="bg-dark bg-opacity-50 p-5 rounded border border-dashed border-warning mb-8">
                                            <p class="text-gray-400 mb-2">Mã Voucher của sếp:</p>
                                            <code class="fs-1 fw-bold text-info user-select-all" id="prize_code">VTKT_XXXXXX</code>
                                        </div>
                                        <a href="{{ route('home') }}" class="btn btn-success btn-lg px-12 py-4">DÙNG NGAY 🚀</a>
                                    </div>
                                @endif
                            </div>

                            <!-- Kho Voucher (Voucher Wallet) -->
                            <div class="card bg-dark bg-opacity-30 border border-white border-opacity-10 mb-6">
                                <div class="card-header border-0 pt-5 min-h-50px">
                                    <h3 class="card-title">
                                        <span class="card-label fw-bold text-white fs-4">🎒 Kho Voucher của Sếp</span>
                                    </h3>
                                </div>
                                <div class="card-body py-3">
                                    <div class="table-responsive" style="max-height: 200px;">
                                        <table class="table table-row-dashed table-row-gray-800 align-middle gs-0 gy-3">
                                            <thead>
                                                <tr class="fw-bold text-muted fs-8 text-uppercase">
                                                    <th class="min-w-100px">Mã Voucher</th>
                                                    <th class="min-w-80px text-center">Giá Trị</th>
                                                    <th class="min-w-100px text-end">Trạng Thái</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($userVouchers as $uv)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <span class="text-white fw-bold fs-7 hover-primary cursor-pointer" onclick="copyToClipboard('{{ $uv->code }}')">{{ $uv->code }}</span>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="text-success fw-bold fs-7">{{ number_format($uv->value) }}đ</span>
                                                    </td>
                                                    <td class="text-end">
                                                        @if($uv->is_used)
                                                            <span class="badge badge-light-danger fs-9">Đã dùng</span>
                                                        @elseif($uv->expires_at && $uv->expires_at->isPast())
                                                            <span class="badge badge-light-dark fs-9">Hết hạn</span>
                                                        @else
                                                            <span class="badge badge-light-success fs-9">Sẵn sàng</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="3" class="text-center text-muted py-5 fs-7">Sếp chưa có voucher nào.</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Global Vouchers Section (Reduced height) -->
                            <div class="card bg-dark bg-opacity-10 border border-white border-opacity-5">
                                <div class="card-body p-5">
                                    <h4 class="text-white fs-6 mb-4">🎫 Voucher Toàn Trang</h4>
                                    <div class="row g-3">
                                        @forelse($globalVouchers as $gv)
                                        <div class="col-sm-6">
                                            <div class="d-flex align-items-center bg-dark bg-opacity-30 rounded p-3 border border-white border-opacity-10">
                                                <div class="flex-grow-1">
                                                    <div class="d-flex justify-content-between">
                                                        <span class="text-white fw-bold fs-7">{{ $gv->code }}</span>
                                                        <span class="text-success fw-bold fs-8">-{{ number_format($gv->value) }}đ</span>
                                                    </div>
                                                    <a href="javascript:;" class="text-primary fs-9 hover-primary" onclick="copyToClipboard('{{ $gv->code }}')">Sao chép</a>
                                                </div>
                                            </div>
                                        </div>
                                        @empty
                                        <div class="col-12 text-muted fs-8">Hiện chưa có voucher chung nào.</div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- RIGHT COLUMN: SPENDING PROGRESS -->
                        <div class="col-lg-3">
                            <div class="card rank-card h-100">
                                <div class="card-header border-0 pt-5">
                                    <h3 class="card-title align-items-start flex-column">
                                        <span class="card-label fw-bold text-white fs-3">📈 Tiến Độ Chi Tiêu</span>
                                        <div class="d-flex align-items-center mt-1">
                                            <span class="text-muted fw-semibold fs-7 me-1">Sếp đã chi tiêu: <b class="text-success">{{ number_format($userTotalSpent) }}đ</b></span>
                                            <i class="fas fa-info-circle text-muted fs-9" data-bs-toggle="tooltip" title="Tổng chi của các đơn hàng đã thanh toán thành công"></i>
                                        </div>
                                    </h3>
                                </div>
                                <div class="card-body pt-5">
                                    <div class="milestone-track mt-2">
                                        @php
                                            $maxMilestone = $milestones[count($milestones)-1]['amount'];
                                            $progressHeight = ($userTotalSpent / $maxMilestone) * 100;
                                            $progressHeight = min(100, $progressHeight);
                                        @endphp
                                        <div class="milestone-progress" style="height: {{ $progressHeight }}%"></div>
                                        
                                        @foreach($milestones as $m)
                                        <div class="milestone-item {{ $userTotalSpent >= $m['amount'] ? 'achieved' : '' }}">
                                            <div class="milestone-dot"></div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <span class="d-block fw-bold text-white fs-7">{{ $m['reward_text'] }}</span>
                                                    <span class="text-muted fs-8">Mốc: {{ number_format($m['amount']) }}đ</span>
                                                </div>
                                                <div class="ms-2">
                                                    @if($m['is_claimed'])
                                                        <span class="badge badge-light-success fs-9"><i class="fas fa-check me-1 fs-9"></i> Đã Nhận</span>
                                                    @elseif($userTotalSpent >= $m['amount'])
                                                        <button class="btn btn-sm btn-primary py-1 px-3 fs-9 btn-claim-milestone" data-id="{{ $m['id'] }}">NHẬN</button>
                                                    @else
                                                        <span class="badge badge-light-dark fs-9">Còn {{ number_format($m['amount'] - $userTotalSpent) }}đ</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    
                                    <div class="alert alert-dismissible bg-light-info d-flex flex-column flex-sm-row p-4 mt-5">
                                        <i class="fas fa-info-circle fs-2hx text-info me-4 mb-5 mb-sm-0"></i>
                                        <div class="d-flex flex-column pe-0 pe-sm-10">
                                            <h5 class="mb-1 text-info fs-7">Về các mốc thưởng:</h5>
                                            <span class="fs-8">Voucher sẽ được hệ thống tự động gửi khi sếp đạt mốc chi tiêu tương ứng.</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
<script>
$(document).ready(function() {
    var isDrawing = false;

    $('#btn_draw, #gift_box').on('click', function() {
        if (isDrawing) return;
        
        isDrawing = true;
        $('#gift_box').addClass('shaking');
        $('#btn_draw').prop('disabled', true).text('ĐANG KIỂM TRA...');

        $.ajax({
            url: "{{ route('api.claim-gift') }}",
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                fingerprint: localStorage.getItem('device_fp') || ''
            },
            success: function(res) {
                if (res.success) {
                    setTimeout(function() {
                        $('#draw_area').fadeOut(300, function() {
                            $('#prize_value').text('Voucher ' + res.value.toLocaleString() + 'đ');
                            $('#prize_code').text(res.voucher);
                            $('#prize_result').fadeIn();
                            
                            // Pháo hoa ăn mừng
                            var duration = 5 * 1000;
                            var animationEnd = Date.now() + duration;
                            var defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 0 };
                            function randomInRange(min, max) { return Math.random() * (max - min) + min; }
                            var interval = setInterval(function() {
                                var timeLeft = animationEnd - Date.now();
                                if (timeLeft <= 0) { return clearInterval(interval); }
                                var particleCount = 50 * (timeLeft / duration);
                                confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.1, 0.3), y: Math.random() - 0.2 } }));
                                confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.7, 0.9), y: Math.random() - 0.2 } }));
                            }, 250);
                        });
                    }, 1500);
                } else {
                    isDrawing = false;
                    $('#gift_box').removeClass('shaking');
                    $('#btn_draw').prop('disabled', false).text('MỞ QUÀ NGAY ✨');
                    toastr.error(res.message);
                }
            },
            error: function() {
                isDrawing = false;
                $('#gift_box').removeClass('shaking');
                $('#btn_draw').prop('disabled', false).text('MỞ QUÀ NGAY ✨');
                toastr.error("Lỗi hệ thống, vui lòng thử lại!");
            }
        });
    });
    // Xử lý Nhận Quà Milestone
    $('.btn-claim-milestone').on('click', function() {
        var btn = $(this);
        var milestoneId = btn.data('id');
        
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>...');

        $.ajax({
            url: "{{ route('api.claim-milestone') }}",
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                milestone_id: milestoneId
            },
            success: function(res) {
                if (res.success) {
                    // Hiệu ứng pháo hoa nhẹ
                    confetti({
                        particleCount: 150,
                        spread: 70,
                        origin: { y: 0.6 }
                    });

                    // Thông báo thành công kiểu Swall (nếu có) hoặc Toastr
                    Swal.fire({
                        title: 'Chúc mừng sếp!',
                        text: res.message + '. Mã voucher: ' + res.code,
                        icon: 'success',
                        confirmButtonText: 'Tuyệt vời!'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    btn.prop('disabled', false).text('NHẬN');
                    toastr.error(res.message);
                }
            },
            error: function() {
                btn.prop('disabled', false).text('NHẬN');
                toastr.error("Có lỗi xảy ra, sếp thử lại nhé!");
            }
        });
    });

    // Xử lý Nhận Thưởng Top tháng trước
    $('#btn_claim_top').on('click', function() {
        var btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Đang xử lý...');

        $.ajax({
            url: "{{ route('api.claim-top-reward') }}",
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}"
            },
            success: function(res) {
                if (res.success) {
                    confetti({
                        particleCount: 200,
                        spread: 80,
                        origin: { y: 0.6 }
                    });

                    Swal.fire({
                        title: 'VINH DANH ĐẠI GIA!',
                        text: res.message,
                        icon: 'success',
                        confirmButtonText: 'XEM VÍ VOUCHER 🎒'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    btn.prop('disabled', false).text('NHẬN VOUCHER ✨');
                    toastr.error(res.message);
                }
            },
            error: function() {
                btn.prop('disabled', false).text('NHẬN VOUCHER ✨');
                toastr.error("Có lỗi xảy ra, sếp thử lại nhé!");
            }
        });
    });
});

function copyToClipboard(text) {
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val(text).select();
    document.execCommand("copy");
    $temp.remove();
    toastr.success("Đã sao chép mã: " + text);
}
</script>
@endsection
