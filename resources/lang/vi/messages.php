<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Messages Language Lines (Vietnamese)
    |--------------------------------------------------------------------------
    |
    | The following language lines are used for various messages throughout
    | the application. You are free to modify these language lines according
    | to your application's requirements.
    |
    */

    // OTP Verification Messages
    'otp' => [
        'session_expired' => 'Phiên làm việc đã hết hạn. Vui lòng thử lại.',
        'user_not_found' => 'Không tìm thấy người dùng.',
        'verified_success' => 'Email đã được xác thực thành công!',
        'invalid_or_expired' => 'Mã OTP không hợp lệ hoặc đã hết hạn.',
        'resent_success' => 'Mã OTP đã được gửi lại đến email của bạn.',
        'sent_success' => 'Mã OTP đã được gửi đến email của bạn.',
        'no_otp_found' => 'Không tìm thấy mã OTP.',
        'expired' => 'Mã OTP đã hết hạn.',
        'invalid' => 'Mã OTP không hợp lệ.',
        'verified_set_password' => 'Mã OTP đã được xác thực. Vui lòng đặt mật khẩu mới của bạn.',
        'verify_first' => 'Vui lòng xác thực mã OTP trước.',
    ],

    // Password Reset Messages
    'password_reset' => [
        'success' => 'Mật khẩu đã được đặt lại thành công. Vui lòng đăng nhập bằng mật khẩu mới của bạn.',
        'user_not_found' => 'Không tìm thấy người dùng.',
    ],

    // Google Auth Messages
    'google' => [
        'login_success' => 'Đăng nhập thành công bằng Google!',
        'linked_success' => 'Tài khoản Google đã được liên kết thành công!',
        'created_success' => 'Tài khoản đã được tạo và đăng nhập thành công bằng Google!',
        'auth_failed' => 'Xác thực với Google thất bại. Vui lòng thử lại.',
    ],

    // Registration Messages
    'registration' => [
        'success' => 'Đăng ký thành công! Vui lòng kiểm tra email của bạn để nhận mã OTP.',
    ],

    // Brand Messages
    'brand' => [
        'created' => 'Thương hiệu đã được tạo thành công!',
        'updated' => 'Thương hiệu đã được cập nhật thành công!',
        'deleted' => 'Thương hiệu đã được xóa thành công!',
        'member_added' => 'Thành viên đã được thêm thành công!',
        'member_removed' => 'Thành viên đã được xóa thành công!',
        'member_updated' => 'Quyền thành viên đã được cập nhật!',
        'cannot_remove_self' => 'Bạn không thể xóa chính mình khỏi thương hiệu.',
        'cannot_remove_owner' => 'Bạn không thể xóa chủ sở hữu thương hiệu.',
    ],

    // Subscription Messages
    'subscription' => [
        'activated' => 'Đã kích hoạt gói :plan thành công!',
        'cancelled' => 'Đã hủy gói thành công.',
        'trial_used' => 'Thương hiệu này đã sử dụng gói dùng thử.',
        'no_active' => 'Không có gói đang hoạt động.',
        'not_found' => 'Không tìm thấy gói cần thanh toán.',
    ],

    // Payment Messages
    'payment' => [
        'transfer_info' => 'Vui lòng chuyển khoản theo thông tin bên dưới.',
        'already_processed' => 'Giao dịch này đã được xử lý.',
        'success' => 'Thanh toán thành công! Gói dịch vụ đã được kích hoạt.',
        'not_received' => 'Chưa nhận được thanh toán. Vui lòng kiểm tra lại sau.',
    ],

    // Ladipage Messages
    'ladipage' => [
        'secret_key_invalid' => 'Khóa bí mật không hợp lệ',
        'api_key_invalid' => 'Khóa API không hợp lệ',
        'ladi_id_empty' => 'ID Ladipage trống',
        'content_empty' => 'Nội dung trống',
        'slug_invalid' => 'Slug :slug không hợp lệ',
        'save_error' => 'Lỗi khi lưu dữ liệu: :error',
        'created' => 'Trang Ladipage đã được tạo thành công!',
        'updated' => 'Trang Ladipage đã được cập nhật thành công!',
        'deleted' => 'Trang Ladipage đã được xóa thành công!',
    ],

];
