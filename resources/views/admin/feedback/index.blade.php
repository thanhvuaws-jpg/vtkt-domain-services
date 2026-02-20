@extends('layouts.admin')

@section('content')
<div class="col-span-12 mt-6">
    <div class="intro-y block sm:flex items-center h-10">
        <h2 class="text-lg font-medium truncate mr-5"> Quản Lý Phản Hồi </h2>
        <div class="flex items-center sm:ml-auto mt-3 sm:mt-0">
            @if($pendingCount > 0)
                <span class="btn btn-danger box flex items-center" style="background-color: #dc2626 !important; color: #ffffff !important;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-bell w-4 h-4 mr-2">
                        <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                    {{ $pendingCount }} phản hồi chờ xử lý
                </span>
            @endif
        </div>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success show mb-2 mt-4" role="alert">
            {{ session('success') }}
        </div>
    @endif
    
    <div class="intro-y overflow-auto lg:overflow-visible mt-8 sm:mt-0">
        <div class="grid grid-cols-1 gap-6">
            @if($feedbacks->isEmpty())
                <div class="intro-y box p-8 text-center">
                    <div class="text-gray-500">Chưa có phản hồi nào</div>
                </div>
            @else
                @foreach($feedbacks as $feedback)
                    <div class="intro-y box p-5 {{ $feedback->status == 0 ? 'border-l-4 border-l-warning' : ($feedback->status == 1 ? 'border-l-4 border-l-success' : '') }}">
                        <div class="flex items-start">
                            <div class="flex-1">
                                <div class="flex items-center mb-3">
                                    <span class="badge badge-{{ $feedback->status == 0 ? 'warning' : ($feedback->status == 1 ? 'success' : 'secondary') }} mr-2">
                                        @if($feedback->status == 0)
                                            Chờ xử lý
                                        @elseif($feedback->status == 1)
                                            Đã trả lời
                                        @else
                                            Đã đọc
                                        @endif
                                    </span>
                                    <span class="text-gray-500 text-sm">{{ $feedback->time }}</span>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="text-sm text-gray-500 mb-1">👤 Người dùng:</div>
                                    <div class="font-medium">{{ $feedback->username }} 
                                        @if($feedback->uid > 0)
                                            <span class="text-gray-400">(ID: {{ $feedback->uid }})</span>
                                        @else
                                            <span class="text-gray-400">(Chưa đăng ký)</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="text-sm text-gray-500 mb-1">📧 Email:</div>
                                    <div class="font-medium">{{ $feedback->email }}</div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="text-sm text-gray-500 mb-1">💬 Phản hồi:</div>
                                    <div class="bg-gray-100 p-3 rounded">{!! nl2br(e($feedback->message)) !!}</div>
                                </div>
                                
                                @if(!empty($feedback->admin_reply))
                                    <div class="mb-3 border-t pt-3">
                                        <div class="text-sm mb-1" style="color: #1e40af !important; font-weight: 600;">✅ Phản hồi từ Admin:</div>
                                        <div class="p-3 rounded" style="background-color: #dbeafe !important; color: #1e40af !important;">{!! nl2br(e($feedback->admin_reply)) !!}</div>
                                        <div class="text-sm mt-1" style="color: #6b7280 !important;">{{ $feedback->reply_time }}</div>
                                    </div>
                                @endif
                                
                                @if($feedback->status == 0)
                                    <div class="mt-4">
                                        <button type="button" class="btn btn-primary" data-tw-toggle="modal" data-tw-target="#reply-modal-{{ $feedback->id }}">
                                            Trả Lời
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Modal Trả Lời -->
                    <div id="reply-modal-{{ $feedback->id }}" class="modal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h2 class="font-medium text-base mr-auto">Trả Lời Phản Hồi</h2>
                                </div>
                                <form method="POST" action="{{ route('admin.feedback.reply', $feedback->id) }}">
                                    @csrf
                                    <div class="modal-body p-5">
                                        <div class="mb-3">
                                            <label class="form-label">Người dùng:</label>
                                            <div class="form-control">{{ $feedback->username }} ({{ $feedback->email }})</div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Phản hồi:</label>
                                            <div class="form-control bg-gray-100">{!! nl2br(e($feedback->message)) !!}</div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Nội dung trả lời: <span class="text-danger">*</span></label>
                                            <textarea name="admin_reply" class="form-control" rows="5" required placeholder="Nhập nội dung trả lời..."></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary w-24 mr-1">Đóng</button>
                                        <button type="submit" class="btn btn-primary w-24">Gửi</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
@endsection
