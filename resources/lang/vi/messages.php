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

    // Brand Show Page Messages
    'brand_show' => [
        'update_brand' => 'Cập nhật thương hiệu',
        'manage_plan' => 'Quản lý gói',
        'manage_members' => 'Quản lý thành viên',
        'payment_history' => 'Lịch sử thanh toán',
        'energy_stats' => 'Thống kê năng lượng',
        'delete_brand' => 'Xóa thương hiệu',
        'founded_year' => 'Năm thành lập',
        'completed' => 'Hoàn thành',
        'industry' => 'Ngành nghề',
        'target_market' => 'Thị trường mục tiêu',
        'energy' => 'Năng lượng',
        'members' => 'Thành viên',
        'brand_journey' => 'Hành Trình Thương Hiệu',
        'track_progress' => 'Theo dõi tiến độ phát triển thương hiệu',
        'stage_1' => 'Giai đoạn 1',
        'stage_2' => 'Giai đoạn 2',
        'stage_3' => 'Giai đoạn 3',
        'root' => 'Gốc Cây',
        'brand_foundation' => 'Nền tảng Thương Hiệu',
        'trunk' => 'Thân Cây',
        'brand_identity' => 'Bản sắc Thương Hiệu',
        'canopy' => 'Tán Cây',
        'growth_spread' => 'Phát triển & Lan tỏa',
        'progress' => 'Tiến độ',
        'stage_completed' => 'Hoàn thành',
        'in_progress' => 'Đang thực hiện',
        'not_unlocked' => 'Chưa mở khóa',
        'brand_overview' => 'Tổng Quan Thương Hiệu',
        'brand_overview_desc' => 'Phân tích hiệu suất và tiến độ xây dựng thương hiệu.',
        'positioning' => 'Định vị',
        'identity' => 'Bản sắc',
        'overall_score' => 'Điểm tổng thể',
        'developing' => 'Đang phát triển',
        'completion' => 'Hoàn thiện',
        'strategy_results' => 'Kết Quả Chiến Lược',
        'authentic_foundation' => 'Nền Tảng Chân Thực',
        'core_values_defined' => 'Giá trị cốt lõi đã được xác định',
        'quality' => 'Chất lượng',
        'dedication' => 'Tận tâm',
        'innovation' => 'Đổi mới',
        'consistent_identity' => 'Bản Sắc Nhất Quán',
        'trunk_not_completed' => 'Chưa hoàn thành giai đoạn Thân Cây',
        'brand_health' => 'Sức Khỏe Thương Hiệu',
        'canopy_not_started' => 'Chưa bắt đầu giai đoạn Tán Cây',
        'next_step' => 'Bước Tiếp Theo',
        'next_step_desc' => 'Tuyệt vời! Giờ là lúc tạo bản sắc độc đáo.',
        'next_step_detail' => 'Hãy tiếp tục hoàn thiện Bản sắc Thương hiệu của bạn. Tạo tuyên bố định vị độc đáo và thiết kế hệ thống nhận diện thị giác nhất quán.',
        'start_now' => 'Bắt đầu ngay',
        'tip' => 'Mẹo: Bạn có thể yêu cầu AI hỗ trợ bất kỳ lúc nào trong quá trình xây dựng thương hiệu',
        'delete_brand_title' => 'Xóa thương hiệu',
        'action_cannot_undo' => 'Hành động này không thể hoàn tác',
        'delete_warning' => 'Bạn sắp xóa thương hiệu <strong>:name</strong>. Tất cả dữ liệu liên quan sẽ bị xóa vĩnh viễn.',
        'confirm_delete_label' => 'Để xác nhận, vui lòng nhập tên thương hiệu:',
        'enter_brand_name' => 'Nhập tên thương hiệu',
        'cancel' => 'Hủy',
        'ai_agents' => 'AI Agents Chuyên Biệt',
        'analyst' => 'Nhà Phân Tích',
        'analyst_desc' => 'Phân tích thị trường và đối thủ cạnh tranh',
        'strategist' => 'Nhà Chiến Lược',
        'strategist_desc' => 'Xây dựng chiến lược định vị thương hiệu',
        'creator' => 'Nhà Sáng Tạo',
        'creator_desc' => 'Tạo nội dung và thiết kế sáng tạo',
        'community' => 'Nhà Cộng Đồng',
        'community_desc' => 'Quản lý và phát triển cộng đồng',
        'interact' => 'Tương tác',
        'unlock_later' => 'Mở khóa sau',
    ],

    // Member Management Messages
    'members' => [
        'title' => 'Quản lý Thành viên',
        'add_member' => 'Thêm thành viên',
        'member_name' => 'Tên thành viên',
        'email' => 'Email',
        'role' => 'Vai trò',
        'status' => 'Trạng thái',
        'joined_date' => 'Ngày tham gia',
        'actions' => 'Hành động',
        'admin' => 'Quản trị viên',
        'editor' => 'Nhà thực thi / Marketing',
        'member' => 'Thành viên',
        'active' => 'Đang hoạt động',
        'edit' => 'Sửa',
        'remove' => 'Gỡ bỏ',
        'no_members' => 'Chưa có thành viên nào',
        'invite_title' => 'Mời thành viên mới',
        'invite_desc' => 'Chọn vai trò phù hợp để phân quyền truy cập và tương tác với AI Agents',
        'member_email' => 'Email thành viên',
        'select_role' => 'Chọn vai trò',
        'admin_desc' => 'Toàn quyền truy cập tất cả các giai đoạn và với AI Agents',
        'editor_desc' => 'Toàn quyền Tán Cây, chỉ xem Gốc và Thân Cây',
        'brand_tree_access' => 'Quyền truy cập Cây Thương hiệu',
        'root_foundation' => 'Gốc Cây (Nền tảng)',
        'trunk_strategy' => 'Thân Cây (Chiến lược)',
        'canopy_execution' => 'Tán Cây (Triển khai)',
        'full_access' => 'Toàn quyền',
        'view_only' => 'Chỉ xem',
        'cancel' => 'Hủy',
        'change_role' => 'Thay đổi vai trò',
        'change_role_desc' => 'Chọn vai trò mới cho thành viên',
        'admin_full_access' => 'Toàn quyền truy cập',
        'editor_full_trunk' => 'Toàn quyền Tán Cây',
        'update' => 'Cập nhật',
        'remove_member' => 'Gỡ bỏ thành viên',
        'remove_confirm' => 'Bạn có chắc chắn muốn gỡ bỏ',
        'from_brand' => 'khỏi thương hiệu?',
    ],

    // Brand Form Messages
    'brand_form' => [
        'add_title' => 'Thêm thương hiệu mới',
        'edit_title' => 'Cập nhật thương hiệu',
        'brand_name' => 'Tên thương hiệu',
        'brand_name_placeholder' => 'Nhập tên thương hiệu',
        'industry' => 'Ngành nghề',
        'industry_placeholder' => 'Nhập ngành nghề',
        'target_market' => 'Thị trường mục tiêu',
        'target_market_placeholder' => 'Nhập thị trường mục tiêu',
        'founded_year' => 'Năm thành lập',
        'founded_year_placeholder' => 'Nhập năm thành lập',
        'description' => 'Mô tả',
        'description_placeholder' => 'Nhập mô tả',
        'logo' => 'Logo thương hiệu',
        'logo_upload_text' => 'Nhấn để chọn hoặc kéo thả',
        'logo_upload_hint' => 'PNG, JPG, GIF (tối đa 2MB)',
        'submit_add' => 'Thêm ngay',
        'submit_update' => 'Cập nhật',
    ],

    // Dashboard Messages
    'dashboard' => [
        'active_brands' => 'Thương hiệu hoạt động',
        'total_brands_desc' => 'Tổng số thương hiệu bạn đang quản lý',
        'need_care' => 'Cần chăm sóc',
        'need_care_desc' => 'Các thương hiệu đang chờ bạn quay lại',
        'growing' => 'Đang tăng trưởng',
        'growing_desc' => 'Các thương hiệu đang chờ phát triển',
        'completed' => 'Đã hoàn thiện',
        'completed_desc' => 'Sẵn sàng để khai thác',
        'search_placeholder' => 'Tìm kiếm thương hiệu...',
        'all_status' => 'Tất cả trạng thái',
        'status_seedling' => 'Cần chăm sóc',
        'status_growing' => 'Đang tăng trưởng',
        'status_completed' => 'Đã hoàn thiện',
        'sort_updated' => 'Cập nhật gần nhất',
        'sort_newest' => 'Mới nhất',
        'add_brand' => 'Thêm thương hiệu',
        'no_brands_found' => 'Không tìm thấy thương hiệu nào',
        'progress' => 'Tiến độ phát triển',
        'root' => 'Gốc',
        'trunk' => 'Thân',
        'next_step' => 'Bước tiếp theo',
        'manage_brand' => 'Quản lý thương hiệu',
        'updated_at' => 'Cập nhật',
    ],

];
