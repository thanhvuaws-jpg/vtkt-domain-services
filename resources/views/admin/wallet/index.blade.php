@extends('layouts.admin')

@section('title', 'Đơn Nạp Ví')

@section('content')
<div class="intro-y box mt-5">
    <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
        <h2 class="font-medium text-base mr-auto">Đơn Nạp Ví - Cộng Tiền Thủ Công</h2>
    </div>
    <div id="horizontal-form" class="p-5">
        <div class="preview">
            @if(session('success'))
                <div class="alert alert-success mb-4">
                    <script>
                        swal("Thông Báo", "{{ session('success') }}", "success");
                    </script>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger mb-4">
                    <script>
                        swal("Lỗi", "{{ session('error') }}", "error");
                    </script>
                </div>
            @endif
            
            @if ($errors->any())
                <div class="alert alert-danger mb-4">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form action="{{ route('admin.wallet.add-balance') }}" method="post">
                @csrf
                <div class="form-inline">
                    <label for="horizontal-form-1" class="form-label sm:w-20">ID Người Dùng</label>
                    <input id="horizontal-form-1" type="number" name="idc" class="form-control" placeholder="Nhập ID người dùng" value="{{ old('idc') }}" required>
                </div>
                <div class="form-inline mt-5">
                    <label for="horizontal-form-2" class="form-label sm:w-20">Số Tiền Cộng</label>
                    <input id="horizontal-form-2" type="number" class="form-control" name="price" placeholder="Nhập số tiền cần cộng (VNĐ)" value="{{ old('price') }}" min="0" required>
                </div>
                <div class="sm:ml-20 sm:pl-5 mt-5">
                    <button type="submit" class="btn btn-primary">Cộng Tiền</button>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary ml-2">Hủy</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

