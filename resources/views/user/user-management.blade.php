@extends('layouts.app')

@section('title', 'Quản lý thành viên')

@section('content')

@include('user.partials.user-summary', [
    'planName' => 'Gói chuyên nghiệp – 5 dự án thương hiệu',
    'activeCount' => 3,
    'pendingCount' => 1,
    'usedSlots' => 4,
    'maxSlots' => 5,
    'usagePercent' => 80,
])

@include('user.partials.user-list', [
    'users' => [
        [
            'name' => 'Nguyễn Văn A',
            'email' => 'nguyenvana@company.com',
            'role' => 'Quản trị viên',
            'status' => 'Đang hoạt động',
            'invited_at' => '15/01/2025',
        ],
        [
            'name' => 'Trần Thị B',
            'email' => 'tranb@company.com',
            'role' => 'Marketing',
            'status' => 'Chờ xác nhận',
            'invited_at' => '16/01/2025',
        ],
    ],
])

@endsection
