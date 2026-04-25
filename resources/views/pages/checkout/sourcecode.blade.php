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
                                    Mua Source Code: {{ $sourceCode->name }}
                                </h1>
                                <div class="py-5">
                                    <div class="form fv-plugins-bootstrap5 fv-plugins-framework">
                                        <div class="fv-row mb-10">
                                            <label class="required fw-semibold fs-6 mb-2"> Tên Source Code </label>
                                            <input type="text" class="form-control form-control-solid" value="{{ $sourceCode->name }}" disabled>
                                        </div>
                                        <div class="fv-row mb-10">
                                            <label class="fw-semibold fs-6 mb-2"> Mô Tả </label>
                                            <textarea class="form-control form-control-solid" style="height: 100px" disabled>{{ $sourceCode->description ?? '' }}</textarea>
                                        </div>
                                        @if (!empty($sourceCode->category))
                                        <div class="fv-row mb-10">
                                            <label class="fw-semibold fs-6 mb-2"> Loại </label>
                                            <input type="text" class="form-control form-control-solid" value="{{ $sourceCode->category }}" disabled>
                                        </div>
                                        @endif

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
                                            <span class="indicator-label fs-3 fw-bold">Mua Ngay - <span id="price-display">{{ number_format($sourceCode->price) }}</span>đ</span>
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

<input type="hidden" id="source_code_id" value="{{ $sourceCode->id }}">
<input type="hidden" id="csrf-token" value="{{ csrf_token() }}">

<script type="text/javascript">
$(document).ready(function() {
    var csrfToken = '{{ csrf_token() }}';
    var sourceCodeId = $('#source_code_id').val();
    var originalPrice = {{ $sourceCode->price }};
    var discountValue = 0;

    function refreshDisplay() {
        var finalTotal = Math.max(0, originalPrice - discountValue);
        $('#price-display').text(finalTotal.toLocaleString('vi-VN'));
    }

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
                total: originalPrice,
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
            url: "{{ route('checkout.sourcecode.process') }}",
            type: 'POST',
            data: {
                source_code_id: sourceCodeId,
                voucher: $('#voucher_code').val(),
                _token: csrfToken
            },
            success: function(data) {
                if (data.success) {
                    toastr.success(data.message);
                    if (data.redirect) {
                        setTimeout(function() { window.location.href = data.redirect; }, 1500);
                    }
                } else {
                    $btn.prop("disabled", false).find('.indicator-label').html(originalLabel);
                    toastr.error(data.message);
                }
            },
            error: function(xhr) {
                $btn.prop("disabled", false).find('.indicator-label').html(originalLabel);
                var msg = xhr.responseJSON ? xhr.responseJSON.message : "Có lỗi xảy ra";
                toastr.error(msg);
            }
        });
    });
});
</script>
@endsection
