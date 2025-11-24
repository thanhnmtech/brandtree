# Language Files / File Ngôn Ngữ

This directory contains translation files for the application in both English and Vietnamese.

Thư mục này chứa các file dịch cho ứng dụng bằng cả tiếng Anh và tiếng Việt.

## Available Languages / Ngôn Ngữ Có Sẵn

- **English (en)** - English translations
- **Vietnamese (vi)** - Bản dịch tiếng Việt (mặc định)

## Files / Các File

### Validation Messages (validation.php)
Contains all validation error messages for form inputs.

Chứa tất cả thông báo lỗi validation cho các trường nhập liệu.

### Authentication Messages (auth.php)
Contains authentication-related messages (login, OTP, email verification).

Chứa các thông báo liên quan đến xác thực (đăng nhập, OTP, xác thực email).

### Password Reset Messages (passwords.php)
Contains password reset related messages.

Chứa các thông báo liên quan đến đặt lại mật khẩu.

## Configuration / Cấu Hình

Default locale is set to Vietnamese in `config/app.php`:

Ngôn ngữ mặc định được thiết lập là tiếng Việt trong `config/app.php`:

```php
'locale' => env('APP_LOCALE', 'vi'),
'fallback_locale' => env('APP_FALLBACK_LOCALE', 'vi'),
```

## Usage / Sử Dụng

### In Controllers / Trong Controllers

```php
// Get translated message
$message = __('auth.failed');

// Get validation message with attribute
$message = __('validation.required', ['attribute' => 'email']);
```

### In Blade Templates / Trong Blade Templates

```blade
{{ __('auth.failed') }}
{{ __('validation.required', ['attribute' => __('validation.attributes.email')]) }}
```

### Change Language Dynamically / Thay Đổi Ngôn Ngữ Động

```php
// In controller or middleware
app()->setLocale('en'); // Switch to English
app()->setLocale('vi'); // Switch to Vietnamese
```

## Custom Attributes / Thuộc Tính Tùy Chỉnh

Custom attribute names are defined in `validation.attributes`:

Tên thuộc tính tùy chỉnh được định nghĩa trong `validation.attributes`:

**Vietnamese:**
- `name` → `họ tên`
- `email` → `địa chỉ email`
- `password` → `mật khẩu`
- `password_confirmation` → `xác nhận mật khẩu`
- `phone` → `số điện thoại`
- `otp` → `mã OTP`
- `terms` → `điều khoản và chính sách`

**English:**
- `name` → `name`
- `email` → `email address`
- `password` → `password`
- `password_confirmation` → `password confirmation`
- `phone` → `phone number`
- `otp` → `OTP code`
- `terms` → `terms and conditions`

## Testing / Kiểm Tra

Test validation messages:

```bash
php artisan tinker --execute="echo __('validation.required', ['attribute' => __('validation.attributes.email')]);"
```

Expected output (Vietnamese): `địa chỉ email là bắt buộc.`

Test auth messages:

```bash
php artisan tinker --execute="echo __('auth.failed');"
```

Expected output (Vietnamese): `Thông tin đăng nhập không khớp với hồ sơ của chúng tôi.`

