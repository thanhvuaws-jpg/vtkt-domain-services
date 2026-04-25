@extends('layouts.app')

@section('content')
<div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">
    <div class="app-container container-xxl d-flex flex-row flex-column-fluid">
        <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
            <div class="d-flex flex-column flex-column-fluid">
                <div id="kt_app_content" class="app-content flex-column-fluid">
                    <div class="card mb-5 mb-xl-10">
                    </div>
                    <div class="card card-docs flex-row-fluid mb-2">
                        <div class="card-body fs-6 py-15 px-10 py-lg-15 px-lg-15 text-gray-700">
                            <div class="col-xl-12 mb-5 mb-xl-10">
                                <div class="card card-flush h-lg-100">
                                    <div class="card-header pt-7">
                                        <h3 class="card-title align-items-start flex-column">
                                            <span class="card-label fw-bolder text-gray-800">Nạp Tiền</span>
                                            <span class="text-gray-400 mt-1 fw-bold fs-6">Qua Nhiều Phương Thức</span>
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <ul class="nav nav-pills nav-pills-custom row position-relative mx-0 mb-9">
                                            <li class="nav-item col-4 mx-0 p-0">
                                                <a class="nav-link d-flex justify-content-center w-100 border-0 h-100 active" data-bs-toggle="pill" href="#kt_list_widget_10_tab_1">
                                                    <span class="nav-text text-gray-800 fw-bolder fs-6 mb-3"> Ví Momo </span>
                                                    <span class="bullet-custom position-absolute z-index-2 bottom-0 w-100 h-4px bg-primary rounded">
                                                    </span>
                                                </a>
                                            </li>
                                            <li class="nav-item col-4 mx-0 px-0">
                                                <a class="nav-link d-flex justify-content-center w-100 border-0 h-100" data-bs-toggle="pill" href="#kt_list_widget_10_tab_2">
                                                    <span class="nav-text text-gray-800 fw-bolder fs-6 mb-3">Ngân Hàng</span>
                                                    <span class="bullet-custom position-absolute z-index-2 bottom-0 w-100 h-4px bg-primary rounded">
                                                    </span>
                                                </a>
                                            </li>
                                            <li class="nav-item col-4 mx-0 px-0">
                                                <a class="nav-link d-flex justify-content-center w-100 border-0 h-100" data-bs-toggle="pill" href="#kt_list_widget_10_tab_3">
                                                    <span class="nav-text text-gray-800 fw-bolder fs-6 mb-3">Thẻ Cào</span>
                                                    <span class="bullet-custom position-absolute z-index-2 bottom-0 w-100 h-4px bg-primary rounded">
                                                    </span>
                                                </a>
                                            </li>
                                            <span class="position-absolute z-index-1 bottom-0 w-100 h-4px bg-light rounded">
                                            </span>
                                        </ul>
                                        <div class="tab-content">
                                            <!-- Momo Tab -->
                                            <div class="tab-pane fade active show" id="kt_list_widget_10_tab_1">
                                                <div class="row g-5 g-xl-8">
                                                    <div class="col-xl-6">
                                                        <div class="card bg-light-danger h-100 border border-danger border-dashed">
                                                            <div class="card-body">
                                                                <div class="text-center mb-5">
                                                                    <img src="https://static.mservice.io/img/logo-momo.png" class="w-50px mb-3" alt="Momo">
                                                                    <h4 class="text-danger fw-bolder">Nạp Qua Ví Momo</h4>
                                                                </div>
                                                                
                                                                <div class="form-group mb-5">
                                                                    <label class="form-label fw-bold">Nhập số tiền muốn nạp:</label>
                                                                    <div class="input-group input-group-solid">
                                                                        <input type="number" id="momo_amount" class="form-control" placeholder="Tối thiểu 10,000" value="50000">
                                                                        <button class="btn btn-danger btn-generate-qr" data-method="MOMO">Tạo Mã QR</button>
                                                                    </div>
                                                                </div>

                                                                <div id="momo_qr_container" class="text-center d-none">
                                                                    <div class="separator separator-dashed my-5"></div>
                                                                    <img id="momo_qr_img" src="" class="img-fluid rounded shadow-sm mb-3 border border-gray-300" style="max-width: 250px;">
                                                                    <div class="alert bg-light-danger border border-danger p-3">
                                                                        <p class="mb-1">Nội dung: <b class="text-danger fs-4" id="momo_qr_content">...</b></p>
                                                                        <p class="mb-0 fs-7">Vui lòng quét QR và KHÔNG thay đổi nội dung chuyển khoản.</p>
                                                                    </div>
                                                                </div>

                                                                <div id="momo_info_static" class="mt-5">
                                                                    <div class="d-flex align-items-center mb-3">
                                                                        <span class="text-gray-600 fs-7 w-125px">Số Điện Thoại:</span>
                                                                        <span class="fw-bolder text-dark fs-6">0856761038</span>
                                                                    </div>
                                                                    <div class="d-flex align-items-center mb-3">
                                                                        <span class="text-gray-600 fs-7 w-125px">Chủ Tài Khoản:</span>
                                                                        <span class="fw-bolder text-dark fs-6">DAM THANH VU</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-6">
                                                        <div class="alert bg-light-info border border-info border-3 border-dashed d-flex flex-column flex-sm-row w-100 p-8">
                                                            <div class="d-flex flex-column pe-0 pe-sm-10">
                                                                <h4 class="text-info fw-bolder mb-3">🚀 Hướng Dẫn Nạp Auto</h4>
                                                                <div class="d-flex align-items-center mb-2">
                                                                    <span class="badge badge-circle badge-info me-3">1</span>
                                                                    <span>Nhập số tiền và nhấn <b>Tạo Mã QR</b></span>
                                                                </div>
                                                                <div class="d-flex align-items-center mb-2">
                                                                    <span class="badge badge-circle badge-info me-3">2</span>
                                                                    <span>Mở App Momo và quét mã được tạo</span>
                                                                </div>
                                                                <div class="d-flex align-items-center mb-2">
                                                                    <span class="badge badge-circle badge-info me-3">3</span>
                                                                    <span>Tiền sẽ được cộng tự động sau 10s - 1 phút</span>
                                                                </div>
                                                                <div class="separator separator-dashed my-4"></div>
                                                                <p class="text-gray-600 fs-7 mb-0">~ Hệ thống hoạt động 24/7 kể cả ngày lễ.</p>
                                                                <p class="text-gray-600 fs-7 mb-0">~ Vui lòng không thay đổi nội dung chuyển tiền.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Bank Tab -->
                                            <div class="tab-pane fade" id="kt_list_widget_10_tab_2">
                                                <div class="row g-5 g-xl-8">
                                                    <div class="col-xl-6">
                                                        <div class="card bg-light-primary h-100 border border-primary border-dashed">
                                                            <div class="card-body">
                                                                <div class="text-center mb-5">
                                                                    <i class="fas fa-university text-primary fs-3x mb-3"></i>
                                                                    <h4 class="text-primary fw-bolder">Nạp Qua Ngân Hàng (VietQR)</h4>
                                                                </div>
                                                                
                                                                <div class="form-group mb-5">
                                                                    <label class="form-label fw-bold">Nhập số tiền muốn nạp:</label>
                                                                    <div class="input-group input-group-solid">
                                                                        <input type="number" id="bank_amount" class="form-control" placeholder="Tối thiểu 10,000" value="50000">
                                                                        <button class="btn btn-primary btn-generate-qr" data-method="BANKING">Tạo Mã QR</button>
                                                                    </div>
                                                                </div>

                                                                <div id="banking_qr_container" class="text-center d-none">
                                                                    <div class="separator separator-dashed my-5"></div>
                                                                    <img id="banking_qr_img" src="" class="img-fluid rounded shadow-sm mb-3 border border-gray-300" style="max-width: 250px;">
                                                                    <div class="alert bg-light-primary border border-primary p-3">
                                                                        <p class="mb-1">Nội dung: <b class="text-primary fs-4" id="banking_qr_content">...</b></p>
                                                                        <p class="mb-0 fs-7 text-dark">Quét VietQR để nạp tiền tự động (hỗ trợ mọi ngân hàng).</p>
                                                                    </div>
                                                                </div>

                                                                <div id="banking_info_static" class="mt-5">
                                                                    <div class="d-flex flex-stack mb-2">
                                                                        <span class="text-gray-600 fs-7">Ngân hàng:</span>
                                                                        <span class="fw-bolder text-dark fs-6">{{ $settings->bank_id ?? 'BIDV' }}</span>
                                                                    </div>
                                                                    <div class="d-flex flex-stack mb-2">
                                                                        <span class="text-gray-600 fs-7">Số Tài Khoản:</span>
                                                                        <span class="fw-bolder text-dark fs-6">{{ $settings->bank_account_no ?? '6151099464' }}</span>
                                                                    </div>
                                                                    <div class="d-flex flex-stack mb-2">
                                                                        <span class="text-gray-600 fs-7">Chủ Tài Khoản:</span>
                                                                        <span class="fw-bolder text-dark fs-6">{{ $settings->bank_account_name ?? 'DAM THANH VU' }}</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-6">
                                                        <div class="alert bg-light-primary border border-primary border-3 border-dashed d-flex flex-column flex-sm-row w-100 p-8">
                                                            <div class="d-flex flex-column pe-0 pe-sm-10">
                                                                <h4 class="text-primary fw-bolder mb-3">🏦 Nạp Tiền Qua VietQR</h4>
                                                                <div class="d-flex align-items-center mb-2">
                                                                    <span class="badge badge-circle badge-primary me-3">1</span>
                                                                    <span>Nhập số tiền và nhấn <b>Tạo Mã QR</b></span>
                                                                </div>
                                                                <div class="d-flex align-items-center mb-2">
                                                                    <span class="badge badge-circle badge-primary me-3">2</span>
                                                                    <span>Sử dụng App Ngân hàng bất kỳ để quét mã</span>
                                                                </div>
                                                                <div class="d-flex align-items-center mb-2">
                                                                    <span class="badge badge-circle badge-primary me-3">3</span>
                                                                    <span>Tiền sẽ được cộng tự động trong vài giây</span>
                                                                </div>
                                                                <div class="separator separator-dashed my-4 border-primary"></div>
                                                                <p class="text-gray-600 fs-7 mb-0">~ Hệ thống sử dụng công nghệ VietQR 2.0.</p>
                                                                <p class="text-gray-600 fs-7 mb-0">~ Vui lòng giữ nguyên nội dung chuyển khoản để nạp Auto.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Card Tab -->
                                            <div class="tab-pane fade" id="kt_list_widget_10_tab_3">
                                                <div class="row g-5 g-xl-8">
                                                    <div class="col-xl-8">
                                                        <div class="form fv-plugins-bootstrap5 fv-plugins-framework" novalidate="novalidate">
                                                            @csrf
                                                            <div class="mb-10">
                                                                <div class="row">
                                                                    <div class="col-xl-4">
                                                                        <label for="type" class="required form-label">Loại Thẻ</label>
                                                                    </div>
                                                                    <div class="col-xl-8">
                                                                        <select class="form-select" aria-label="Loại Thẻ" id="type">
                                                                            <option>Loại Thẻ</option>
                                                                            <option value="VIETTEL">VIETTEL</option>
                                                                            <option value="VINAPHONE">VINAPHONE</option>
                                                                            <option value="VIETNAMOBILE">VIETNAMOBILE</option>
                                                                            <option value="MOBIFONE">MOBIFONE</option>
                                                                            <option value="GARENA">GARENA</option>
                                                                            <option value="ZING">ZING</option>
                                                                            <option value="GATE">GATE</option>
                                                                        </select>
                                                                        <div class="mb-3">
                                                                        </div>
                                                                        <span class="text-muted required">Vui Lòng Chọn Đúng Loại Thẻ</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="mb-10">
                                                                <div class="row">
                                                                    <div class="col-xl-4">
                                                                        <label for="amount" class="required form-label">Mệnh Giá</label>
                                                                    </div>
                                                                    <div class="col-xl-8">
                                                                        <select class="form-select" aria-label="Mệnh Giá" id="amount">
                                                                            <option>Mệnh Giá</option>
                                                                            <option value="10000">10,000</option>
                                                                            <option value="20000">20,000</option>
                                                                            <option value="30000">30,000</option>
                                                                            <option value="50000">50,000</option>
                                                                            <option value="100000">100,000</option>
                                                                            <option value="200000">200,000</option>
                                                                            <option value="300000">300,000</option>
                                                                            <option value="500000">500,000</option>
                                                                            <option value="1000000">1,000,000</option>
                                                                        </select>
                                                                        <div class="mb-3">
                                                                        </div>
                                                                        <span class="text-muted required">Vui Lòng Điền Đúng Mệnh Giá Thẻ</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="mb-10">
                                                                <div class="row">
                                                                    <div class="col-xl-4">
                                                                        <label for="pin" class="required form-label">Mã Thẻ</label>
                                                                    </div>
                                                                    <div class="col-xl-8">
                                                                        <div class="position-relative">
                                                                            <input type="text" class="form-control form-control-solid" maxlength="30" placeholder="Mã Thẻ" id="pin">
                                                                            <div class="position-absolute translate-middle-y top-50 end-0 me-3">
                                                                                <span class="svg-icon svg-icon-2hx">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                                                        <path d="M22 7H2V11H22V7Z" fill="currentColor">
                                                                                        </path>
                                                                                        <path opacity="0.3" d="M21 19H3C2.4 19 2 18.6 2 18V6C2 5.4 2.4 5 3 5H21C21.6 5 22 5.4 22 6V18C22 18.6 21.6 19 21 19ZM14 14C14 13.4 13.6 13 13 13H5C4.4 13 4 13.4 4 14C4 14.6 4.4 15 5 15H13C13.6 15 14 14.6 14 14ZM16 15.5C16 16.3 16.7 17 17.5 17H18.5C19.3 17 20 16.3 20 15.5C20 14.7 19.3 14 18.5 14H17.5C16.7 14 16 14.7 16 15.5Z" fill="currentColor">
                                                                                        </path>
                                                                                    </svg>
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="mb-10">
                                                                <div class="row">
                                                                    <div class="col-xl-4">
                                                                        <label for="serial" class="required form-label">Số Seri</label>
                                                                    </div>
                                                                    <div class="col-xl-8">
                                                                        <div class="position-relative">
                                                                            <input type="text" class="form-control form-control-solid" maxlength="30" placeholder="Mã Serial Thẻ" id="serial">
                                                                            <div class="position-absolute translate-middle-y top-50 end-0 me-3">
                                                                                <span class="svg-icon svg-icon-2hx">
                                                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                        <path opacity="0.3" d="M12.3408 20.7578C12.3408 21.7578 13.6408 22.0578 14.1408 21.2578L19.5408 11.8578C19.9408 11.1578 19.4408 10.3578 18.6408 10.3578H12.3408V20.7578Z" fill="black">
                                                                                        </path>
                                                                                        <path d="M12.3408 3.9578C12.3408 2.9578 11.0408 2.6578 10.5408 3.4578L5.14078 12.8578C4.74078 13.5578 5.24078 14.3578 6.04078 14.3578H12.3408V3.9578Z" fill="black">
                                                                                        </path>
                                                                                    </svg>
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="mb-10 text-center">
                                                                <div id="status"></div>
                                                                <button type="button" class="btn btn-light-info" id="napthe">
                                                                    <span class="svg-icon svg-icon-1" id="form_btn_svg_icon">
                                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                            <path d="M20 8H16C15.4 8 15 8.4 15 9V16H10V17C10 17.6 10.4 18 11 18H16C16 16.9 16.9 16 18 16C19.1 16 20 16.9 20 18H21C21.6 18 22 17.6 22 17V13L20 8Z" fill="black">
                                                                            </path>
                                                                            <path opacity="0.3" d="M20 18C20 19.1 19.1 20 18 20C16.9 20 16 19.1 16 18C16 16.9 16.9 16 18 16C19.1 16 20 16.9 20 18ZM15 4C15 3.4 14.6 3 14 3H3C2.4 3 2 3.4 2 4V13C2 13.6 2.4 14 3 14H15V4ZM6 16C4.9 16 4 16.9 4 18C4 19.1 4.9 20 6 20C7.1 20 8 19.1 8 18C8 16.9 7.1 16 6 16Z" fill="black">
                                                                            </path>
                                                                        </svg>
                                                                    </span>
                                                                    <span class="indicator-label">
                                                                        Nạp Ngay
                                                                    </span>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-4">
                                                        <div class="alert bg-light-info border border-info border-3 border-dashed d-flex flex-column flex-sm-row w-100 p-5 mb-10">
                                                            <span class="svg-icon svg-icon-2hx svg-icon-info me-4 mb-5 mb-sm-0">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                                    <path opacity="0.3" d="M2 4V16C2 16.6 2.4 17 3 17H13L16.6 20.6C17.1 21.1 18 20.8 18 20V17H21C21.6 17 22 16.6 22 16V4C22 3.4 21.6 3 21 3H3C2.4 3 2 3.4 2 4Z" fill="currentColor">
                                                                    </path>
                                                                    <path d="M18 9H6C5.4 9 5 8.6 5 8C5 7.4 5.4 7 6 7H18C18.6 7 19 7.4 19 8C19 8.6 18.6 9 18 9ZM16 12C16 11.4 15.6 11 15 11H6C5.4 11 5 11.4 5 12C5 12.6 5.4 13 6 13H15C15.6 13 16 12.6 16 12Z" fill="currentColor">
                                                                    </path>
                                                                </svg>
                                                            </span>
                                                            <div class="d-flex flex-column pe-0 pe-sm-10">
                                                                <h4 class="mb-1">Lưu Ý</h4>
                                                                <span>~ Hệ thống nạp tiền tự động 24/7, Thẻ nạp sẽ được cộng sau vài giây.</span>
                                                                <br>
                                                                <span>~ Vui Lòng Điền Đúng Mệnh Giá Thẻ , Nếu sai mệnh giá thẻ sẽ bị trừ 50% giá trị.</span>
                                                                <br>
                                                                <span>~ Nếu sau 15 phút chưa được cộng tiền vào tài khoản hãy ib admin.</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Card History -->
                            <div class="card">
                                <div class="card-header card-header-stretch">
                                    <div class="card-title">
                                        <h3 class="m-0 text-gray-800"> Lịch Sử Nạp Thẻ </h3>
                                    </div>
                                </div>
                                <div id="kt_referred_users_tab_content" class="tab-content">
                                    <div id="kt_referrals_1" class="card-body p-0 tab-pane fade show active" role="tabpanel">
                                        <div class="table-responsive">
                                            <table class="table table-flush align-middle table-row-bordered table-row-solid gy-4 gs-9">
                                                <thead class="border-gray-200 fs-5 fw-semibold bg-lighten">
                                                    <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase">
                                                        <th class="min-w-100px">Mã Giao Dịch</th>
                                                        <th class="min-w-100px"> Mệnh Giá </th>
                                                        <th class="min-w-100px"> Ngày Nạp </th>
                                                        <th class="min-w-100px"> Pin </th>
                                                        <th class="min-w-75px"> Serial </th>
                                                        <th class="min-w-75px"> Loại Thẻ </th>
                                                        <th class="min-w-100px pe-5"> Trạng Thái </th>
                                                    </tr>
                                                </thead>
                                                <tbody class="fs-6 fw-semibold text-gray-600">
                                                    @forelse($cards as $card)
                                                    <tr>
                                                        <td class="ps-9">
                                                            #{{ $card->requestid }}
                                                        </td>
                                                        <td class="ps-0">{{ number_format($card->amount) }}đ
                                                        </td>
                                                        <td class="ps-0">{{ $card->time }}
                                                        </td>
                                                        <td class="ps-0">{{ $card->pin }}
                                                        </td>
                                                        <td class="ps-0">{{ $card->serial }}
                                                        </td>
                                                        <td class="ps-0">{{ $card->type }}
                                                        </td>
                                                        <td class="text-center">
                                                            @if($card->status == 0)
                                                                <button class="btn btn-info"> Đang Gửi Thẻ </button>
                                                            @elseif($card->status == 1)
                                                                <button class="btn btn-success"> Thẻ Đúng </button>
                                                            @elseif($card->status == 2)
                                                                <button class="btn btn-danger"> Thẻ Sai </button>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @empty
                                                    <tr>
                                                        <td colspan="7" class="text-center">Chưa có lịch sử nạp thẻ</td>
                                                    </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
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

@push('scripts')
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<script>
$(document).ready(function() {
    // Xử lý tạo mã QR động cho Bank và Momo
    $('.btn-generate-qr').on('click', function() {
        var btn = $(this);
        var method = btn.data('method');
        var amount = (method === 'BANKING') ? $('#bank_amount').val() : $('#momo_amount').val();
        var originalText = btn.text();
        
        if (amount < 10000) {
            toastr.warning('Số tiền nạp tối thiểu là 10,000₫', 'Thông Báo');
            return;
        }

        btn.prop('disabled', true).text('Đang tạo mã...');

        $.ajax({
            url: '{{ route("api.get-payment-details") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                method: method,
                amount: amount
            },
            success: function(response) {
                btn.prop('disabled', false).text(originalText);
                if (response.success) {
                    var details = response.details;
                    var containerId = (method === 'BANKING') ? '#banking_qr_container' : '#momo_qr_container';
                    var imgId = (method === 'BANKING') ? '#banking_qr_img' : '#momo_qr_img';
                    var contentId = (method === 'BANKING') ? '#banking_qr_content' : '#momo_qr_content';
                    
                    $(imgId).attr('src', details.qr_code);
                    $(contentId).text(details.content);
                    $(containerId).removeClass('d-none');
                    
                    toastr.success('Đã tạo mã QR thành công!', 'Thông Báo');
                    
                    // Cuộn xuống chỗ QR
                    $('html, body').animate({
                        scrollTop: $(containerId).offset().top - 100
                    }, 500);
                } else {
                    toastr.error(response.message, 'Thông Báo');
                }
            },
            error: function() {
                btn.prop('disabled', false).text(originalText);
                toastr.error('Có lỗi xảy ra khi tạo mã QR', 'Thông Báo');
            }
        });
    });

    $('#napthe').on('click', function() {
        var btn = $(this);
        var originalText = btn.find('.indicator-label').text();
        
        // Disable button and show loading
        btn.prop('disabled', true);
        btn.find('.indicator-label').text('Đang xử lý...');
        
        var pin = $('#pin').val();
        var serial = $('#serial').val();
        var amount = $('#amount').val();
        var type = $('#type').val();
        
        $.ajax({
            url: '{{ route("recharge.process") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                pin: pin,
                serial: serial,
                amount: amount,
                type: type
            },
            success: function(response) {
                btn.prop('disabled', false);
                btn.find('.indicator-label').text(originalText);
                
                if (response.success) {
                    toastr.success(response.message, 'Thông Báo');
                    // Reload page after 2 seconds to show updated card history
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                } else {
                    toastr.error(response.message, 'Thông Báo');
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false);
                btn.find('.indicator-label').text(originalText);
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    toastr.error(xhr.responseJSON.message, 'Thông Báo');
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    var errors = xhr.responseJSON.errors;
                    var errorMsg = Object.values(errors).flat().join('<br>');
                    toastr.error(errorMsg, 'Thông Báo');
                } else {
                    toastr.error('Có lỗi xảy ra, vui lòng thử lại', 'Thông Báo');
                }
            }
        });
    });
});
</script>
@endpush
@endsection
