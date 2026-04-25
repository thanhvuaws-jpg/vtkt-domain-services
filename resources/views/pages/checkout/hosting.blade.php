@extends('layouts.app')

@section('content')
<div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">
    <div class="app-container container-xxl d-flex flex-row flex-column-fluid">
        <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
            <div class="d-flex flex-column flex-column-fluid">
                <div id="kt_app_content" class="app-content flex-column-fluid">
                    <div class="card card-docs flex-row-fluid mb-2">
                        <div class="card-body fs-6 py-15 px-10 py-lg-15 px-lg-15 text-gray-700">
                            <div class="py-10">
                                <h1 class="anchor fw-bold mb-5">
                                    Mua Hosting: {{ $hosting->name }}
                                </h1>
                                <div class="py-5">
                                    <div class="form fv-plugins-bootstrap5 fv-plugins-framework">
                                        <div class="fv-row mb-10">
                                            <label class="required fw-semibold fs-6 mb-2"> Tên Gói Hosting </label>
                                            <input type="text" class="form-control form-control-solid" value="{{ $hosting->name }}" disabled>
                                        </div>
                                        <div class="fv-row mb-10">
                                            <label class="fw-semibold fs-6 mb-2"> Mô Tả </label>
                                            <textarea class="form-control form-control-solid" disabled>{{ $hosting->description ?? '' }}</textarea>
                                        </div>
                                        <div class="fv-row mb-10">
                                            <label class="required fw-semibold fs-6 mb-2"> Thời Gian Thuê </label>
                                            <select id="period" class="form-select">
                                                <option value="month">Theo Tháng - {{ number_format($hosting->price_month) }}đ</option>
                                                <option value="year">Theo Năm - {{ number_format($hosting->price_year) }}đ</option>
                                            </select>
                                        </div>

                                        <!-- Voucher Block -->
                                        <div class="fv-row mb-10">
                                            <label class="fw-semibold fs-6 mb-2 text-primary"> <i class="fas fa-ticket-alt me-2"></i> Mã Giảm Giá (Voucher) </label>
                                            <div class="input-group input-group-solid">
                                                <input type="text" id="voucher_code" class="form-control" placeholder="Nhập mã voucher nếu có">
                                                <button class="btn btn-info" type="button" id="apply_voucher">Áp dụng</button>
                                            </div>
                                            <div id="voucher_info" class="mt-2 fw-bold text-success" style="display: none;"></div>
                                        </div>

                                        <div id="status"></div>
                                        
                                        <button id="buy" type="button" class="btn btn-primary w-100 py-4">
                                            <span class="indicator-label fs-3 fw-bold">Mua Ngay - <span id="price-display">{{ number_format($hosting->price_month) }}</span>đ</span>
                                        </button>
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

<!-- PROVISIONING MODAL -->
<div class="modal fade" tabindex="-1" id="provisioningModal" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="modalTitle">Đang khởi tạo gói Hosting...</h3>
            </div>
            <div class="modal-body text-center py-10">
                <div id="progressArea">
                    <div class="spinner-border text-primary mb-5" role="status" style="width: 3rem; height: 3rem;"></div>
                    <div class="progress mb-4" style="height: 20px;">
                        <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 0%">0%</div>
                    </div>
                    <h5 id="progressText" class="text-muted">Đang phân bổ Server...</h5>
                </div>
                <div id="successArea" style="display: none;">
                    <i class="fas fa-check-circle text-success mb-3" style="font-size: 60px;"></i>
                    <h4 class="text-success fw-bold mb-5">Đã cấp phát Hosting thành công!</h4>
                    <div class="bg-light p-5 rounded text-start mb-5" style="border: 1px dashed #ccc;">
                        <div class="mb-3">
                            <span class="text-muted">Server truy cập:</span>
                            <span class="fw-bold ms-2 text-primary" id="hostIp">---</span>
                        </div>
                        <div class="mb-3">
                            <span class="text-muted">Tài khoản:</span>
                            <span class="fw-bold ms-2" id="hostUser">---</span>
                        </div>
                        <div>
                            <span class="text-muted">Mật khẩu:</span>
                            <span class="fw-bold ms-2 user-select-all" id="hostPass" style="background:#fff; padding:2px 5px; border-radius:4px;">---</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" id="modalFooter" style="display: none;">
                <a href="{{ route('manager.index') }}" class="btn btn-primary w-100">Đi đến Quản Lý Dịch Vụ</a>
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="hosting_id" value="{{ $hosting->id }}">
<input type="hidden" id="price-month" value="{{ $hosting->price_month }}">
<input type="hidden" id="price-year" value="{{ $hosting->price_year }}">
<input type="hidden" id="csrf-token" value="{{ csrf_token() }}">

<script type="text/javascript">
$(document).ready(function() {
    var csrfToken = '{{ csrf_token() }}';
    var hostingId = $('#hosting_id').val();
    var discountValue = 0;

    function getBasePrice() {
        var period = $('#period').val();
        var priceMonth = parseInt($('#price-month').val()) || 0;
        var priceYear = parseInt($('#price-year').val()) || 0;
        return period === 'month' ? priceMonth : priceYear;
    }

    function refreshDisplay() {
        var base = getBasePrice();
        var finalTotal = Math.max(0, base - discountValue);
        $('#price-display').text(finalTotal.toLocaleString('vi-VN'));
    }

    $('#period').on('change', function() {
        refreshDisplay();
    });

    $('#apply_voucher').on('click', function() {
        var code = $('#voucher_code').val();
        if (!code) {
            toastr.warning("Vui lòng nhập mã voucher");
            return;
        }

        var $btn = $(this);
        $btn.prop('disabled', true).text('...');

        $.ajax({
            url: "{{ route('ajax.apply-voucher') }}",
            type: 'POST',
            data: {
                code: code,
                total: getBasePrice(),
                _token: csrfToken
            },
            success: function(res) {
                $btn.prop('disabled', false).text('Áp dụng');
                if (res.success) {
                    toastr.success(res.message);
                    discountValue = res.discount;
                    $('#voucher_info').html('<i class="fas fa-check-circle me-1"></i> Đã giảm: -' + res.formatted_discount).fadeIn();
                    refreshDisplay();
                    $('#voucher_code').prop('readonly', true);
                    $btn.hide();
                } else {
                    toastr.error(res.message);
                }
            },
            error: function() {
                $btn.prop('disabled', false).text('Áp dụng');
                toastr.error("Lỗi hệ thống");
            }
        });
    });

    $('#buy').on('click', function() {
        var $btn = $(this);
        var originalLabel = $btn.find('.indicator-label').html();
        
        $btn.prop("disabled", true).find('.indicator-label').text('Đang xử lý...');
        
        $.ajax({
            url: "{{ route('checkout.hosting.process') }}",
            type: 'POST',
            data: {
                hosting_id: hostingId,
                period: $('#period').val(),
                voucher: $('#voucher_code').val(),
                _token: csrfToken
            },
            success: function(data) {
                if (data.success) {
                    toastr.success(data.message);
                    if (data.credentials) {
                        var myModal = new bootstrap.Modal(document.getElementById('provisioningModal'));
                        myModal.show();
                        var progress = 0;
                        var interval = setInterval(function() {
                            progress += 10;
                            $('#progressBar').css('width', progress + '%').text(progress + '%');
                            if (progress >= 100) {
                                clearInterval(interval);
                                setTimeout(function() {
                                    $('#progressArea').hide();
                                    $('#modalTitle').text('Hoàn Tất!');
                                    $('#successArea').fadeIn();
                                    $('#modalFooter').fadeIn();
                                    $('#hostIp').text(data.credentials.ip);
                                    $('#hostUser').text(data.credentials.username);
                                    $('#hostPass').text(data.credentials.password);
                                }, 500);
                            }
                        }, 300);
                    } else if (data.redirect) {
                        window.location.href = data.redirect;
                    }
                } else {
                    $btn.prop("disabled", false).find('.indicator-label').html(originalLabel);
                    toastr.error(data.message);
                }
            },
            error: function() {
                $btn.prop("disabled", false).find('.indicator-label').html(originalLabel);
                toastr.error("Có lỗi xảy ra!");
            }
        });
    });
});
</script>
@endsection
