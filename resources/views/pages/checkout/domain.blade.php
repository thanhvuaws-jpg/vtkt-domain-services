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
                                    Đăng Ký Tên Miền &nbsp; <img src="{{ fixImagePath($domain->image) }}" width="50px">
                                </h1>
                                <div class="py-5">
                                    <div class="form fv-plugins-bootstrap5 fv-plugins-framework">
                                        <div class="fv-row mb-10">
                                            <label class="required fw-semibold fs-6 mb-2"> Tên Miền </label>
                                            <input type="text" id="domain" class="form-control form-control-solid mb-3 mb-lg-0" value="{{ $domainName }}" disabled>
                                        </div>
                                        <div class="fv-row mb-10">
                                            <label class="required fw-semibold fs-6 mb-2"> NS1 </label>
                                            <input type="text" id="ns1" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="NS1 Của Cloudflare">
                                        </div>
                                        <div class="fv-row mb-10">
                                            <label class="required fw-semibold fs-6 mb-2"> NS2 </label>
                                            <input type="text" id="ns2" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="NS2 Của Cloudflare">
                                        </div>
                                        <div class="fv-row mb-10 fv-plugins-icon-container">
                                            <label class="required fw-semibold fs-6 mb-2"> Hạn Dùng </label>
                                            <select id="hsd" class="form-select">
                                                <option value="1"> 1 Năm </option>
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
                                            <span class="indicator-label fs-3 fw-bold">Mua Ngay - <span id="price-display">{{ number_format($price ?? 0) }}</span>đ</span>
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

<input type="hidden" id="checkout-url" value="{{ route('checkout.domain.process') }}">
<input type="hidden" id="profile-url" value="{{ route('profile') }}">
<input type="hidden" id="csrf-token" value="{{ csrf_token() }}">

<script type="text/javascript">
(function() {
    'use strict';
    
    var checkoutUrl = '{{ route("checkout.domain.process") }}';
    var profileUrl = '{{ route("profile") }}';
    var csrfToken = '{{ csrf_token() }}';
    var originalPrice = {{ $price ?? 0 }};
    
    function initVoucher() {
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
                        $('#voucher_info').html('<i class="fas fa-check-circle me-1"></i> Đã giảm: -' + res.formatted_discount).fadeIn();
                        $('#price-display').text(res.formatted_total.replace('đ', ''));
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
    }

    function initBuyButton() {
        $('#buy').on('click', function(e) {
            e.preventDefault();
            var $btn = $(this);
            var domain = $('#domain').val();
            var ns1 = $('#ns1').val();
            var ns2 = $('#ns2').val();
            var hsd = $('#hsd').val();
            var voucher = $('#voucher_code').val();
            
            if (!ns1 || !ns2) {
                toastr.error("Vui lòng nhập đầy đủ NS1 và NS2");
                return;
            }

            $btn.prop("disabled", true).find('.indicator-label').text('Đang xử lý...');
            
            $.ajax({
                url: checkoutUrl,
                type: 'POST',
                data: {
                    domain: domain,
                    ns1: ns1,
                    ns2: ns2,
                    hsd: hsd,
                    voucher: voucher,
                    _token: csrfToken
                },
                success: function(data) {
                    if (data.success) {
                        toastr.success(data.message);
                        setTimeout(function() { window.location.href = profileUrl; }, 1500);
                    } else {
                        $btn.prop("disabled", false).find('.indicator-label').html('Mua Ngay - <span id="price-display">' + $('#price-display').text() + '</span>đ');
                        toastr.error(data.message);
                    }
                },
                error: function(xhr) {
                    $btn.prop("disabled", false).find('.indicator-label').html('Mua Ngay - <span id="price-display">' + $('#price-display').text() + '</span>đ');
                    var msg = xhr.responseJSON ? xhr.responseJSON.message : "Có lỗi xảy ra";
                    toastr.error(msg);
                }
            });
        });
    }

    $(document).ready(function() {
        initVoucher();
        initBuyButton();
    });
})();
</script>
@endsection
