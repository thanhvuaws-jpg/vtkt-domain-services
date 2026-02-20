@extends('layouts.admin')

@section('title', 'Quản Lý Gạch Cards')

@section('content')
<div class="col-span-12 mt-6">
    <div class="intro-y block sm:flex items-center h-10">
        <h2 class="text-lg font-medium truncate mr-5">Quản Lý Gạch Cards</h2>
        <div class="flex items-center sm:ml-auto mt-3 sm:mt-0">
        </div>
    </div>
    <div class="intro-y box">
        <div class="p-5" id="head-options-table">
            <div class="preview">
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead style="background-color: #1e293b !important;">
                            <tr style="background-color: #1e293b !important;">
                                <th class="whitespace-nowrap" style="background-color: #1e293b !important; color: #ffffff !important;">#</th>
                                <th class="whitespace-nowrap" style="background-color: #1e293b !important; color: #ffffff !important;">UID</th>
                                <th class="whitespace-nowrap" style="background-color: #1e293b !important; color: #ffffff !important;">Mã Thẻ</th>
                                <th class="whitespace-nowrap" style="background-color: #1e293b !important; color: #ffffff !important;">Serial</th>
                                <th class="whitespace-nowrap" style="background-color: #1e293b !important; color: #ffffff !important;">Mệnh Giá</th>
                                <th class="whitespace-nowrap" style="background-color: #1e293b !important; color: #ffffff !important;">Loại Thẻ</th>
                                <th class="whitespace-nowrap" style="background-color: #1e293b !important; color: #ffffff !important;">Status</th>
                                <th class="whitespace-nowrap" style="background-color: #1e293b !important; color: #ffffff !important;">Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($cards->isEmpty())
                                <tr>
                                    <td colspan="8" class="text-center" style="color: #334155 !important;">Chưa có thẻ cào nào</td>
                                </tr>
                            @else
                                @foreach($cards as $index => $card)
                                <tr>
                                    <td style="color: #334155 !important;">#{{ $index + 1 }}</td>
                                    <td style="color: #334155 !important;">{{ $card->uid }}</td>
                                    <td style="color: #334155 !important;">{{ $card->pin }}</td>
                                    <td style="color: #334155 !important;">{{ $card->serial }}</td>
                                    <td style="color: #334155 !important;">{{ number_format($card->amount) }}đ</td>
                                    <td style="color: #334155 !important;">{{ $card->type }}</td>
                                    <td>
                                        @if($card->status == 0)
                                            <button class="btn btn-primary">Đang Duyệt</button>
                                        @elseif($card->status == 1)
                                            <button class="btn btn-success">Thẻ Đúng</button>
                                        @elseif($card->status == 2)
                                            <button class="btn btn-danger">Thẻ Sai</button>
                                        @elseif($card->status == 3)
                                            <button class="btn btn-warning">Sai Mệnh Giá</button>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap" style="color: #334155 !important;">{{ $card->time }}</td>
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
@endsection

