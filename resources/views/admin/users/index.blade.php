@extends('layouts.admin')

@section('title', 'Danh Sách Thành Viên')

@section('content')
<div class="col-span-12 mt-6">
    <div class="intro-y block sm:flex items-center h-10">
        <h2 class="text-lg font-medium truncate mr-5">Danh Sách Thành Viên</h2>
        <div class="flex items-center sm:ml-auto mt-3 sm:mt-0">
        </div>
    </div>
    <div class="intro-y box">
        <div class="p-5" id="head-options-table">
            <div class="preview">
                @if(session('success'))
                    <div class="alert alert-success mb-4">
                        <script>
                            swal("Thông Báo", "{{ session('success') }}", "success");
                        </script>
                    </div>
                @endif
                
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead style="background-color: #1e293b !important;">
                            <tr style="background-color: #1e293b !important;">
                                <th class="whitespace-nowrap" style="background-color: #1e293b !important; color: #ffffff !important;">#</th>
                                <th class="whitespace-nowrap" style="background-color: #1e293b !important; color: #ffffff !important;">UID</th>
                                <th class="whitespace-nowrap" style="background-color: #1e293b !important; color: #ffffff !important;">Tài Khoản</th>
                                <th class="whitespace-nowrap" style="background-color: #1e293b !important; color: #ffffff !important;">Mật Khẩu</th>
                                <th class="whitespace-nowrap" style="background-color: #1e293b !important; color: #ffffff !important;">Tiền</th>
                                <th class="whitespace-nowrap" style="background-color: #1e293b !important; color: #ffffff !important;">Time</th>
                                <th class="whitespace-nowrap" style="background-color: #1e293b !important; color: #ffffff !important;">Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($users->isEmpty())
                                <tr>
                                    <td colspan="7" class="text-center" style="color: #334155 !important;">Chưa có thành viên nào</td>
                                </tr>
                            @else
                                @foreach($users as $index => $user)
                                <tr>
                                    <td style="color: #334155 !important;">#{{ $index + 1 }}</td>
                                    <td style="color: #334155 !important;">{{ $user->id }}</td>
                                    <td style="color: #334155 !important;">{{ $user->taikhoan }}</td>
                                    <td style="color: #334155 !important;">{{ $user->matkhau }}</td>
                                    <td style="color: #334155 !important;">{{ number_format($user->tien) }}đ</td>
                                    <td class="whitespace-nowrap" style="color: #334155 !important;">{{ $user->time }}</td>
                                    <td>
                                        <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                                            <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-primary btn-sm w-full sm:w-auto">Xem</a>
                                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-warning btn-sm w-full sm:w-auto">Sửa</a>
                                            <button data-tw-toggle="modal" data-tw-target="#header-footer-modal-preview-{{ $user->id }}" class="btn btn-success btn-sm w-full sm:w-auto">Số Dư</button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@foreach($users as $user)
<div id="header-footer-modal-preview-{{ $user->id }}" class="modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="font-medium text-base mr-auto">Chỉnh Sửa Tài Khoản ({{ $user->taikhoan }})</h2>
                <div class="dropdown sm:hidden">
                    <a class="dropdown-toggle w-5 h-5 block" href="javascript:;" aria-expanded="false" data-tw-toggle="dropdown">
                        <i data-lucide="more-horizontal" class="w-5 h-5 text-slate-500"></i>
                    </a>
                    <div class="dropdown-menu w-40">
                    </div>
                </div>
            </div>
            <form action="{{ route('admin.users.update-balance', $user->id) }}" method="post">
                @csrf
                @method('PUT')
                <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                    <div class="col-span-12">
                        <label for="modal-form-1" class="form-label">Số Dư</label>
                        <input type="text" name="tien" class="form-control" rows="4" cols="50" placeholder="Số Dư" value="{{ $user->tien }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary w-20 mr-1">Cancel</button>
                    <button type="submit" class="btn btn-primary w-20">Gửi Đi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection

