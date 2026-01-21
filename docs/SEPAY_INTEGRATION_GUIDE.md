# Hướng dẫn tích hợp thanh toán Sepay

## Tổng quan

Hệ thống đã được tích hợp đầy đủ với Sepay Payment Gateway để tự động duyệt đơn hàng mua gói dịch vụ thông qua chuyển khoản ngân hàng.

## Cách hoạt động

### 1. Quy trình thanh toán

```
User chọn gói → Tạo Payment → Hiển thị QR Code →
User chuyển khoản → Sepay Webhook → Tự động kích hoạt gói
```

### 2. Mã thanh toán (Payment Code)

**Format:** `BT{brand_id}{payment_id}`

**Ví dụ:**
- Brand ID: 1 → Padding: 0001
- Payment ID: 123 → Padding: 000123
- **Mã thanh toán:** `BT0001000123`

### 3. Tự động kích hoạt

Khi Sepay gửi webhook với nội dung chuyển khoản chứa mã thanh toán, hệ thống sẽ:

1. ✅ Verify payment code
2. ✅ Check số tiền chuyển khoản >= giá gói
3. ✅ Cập nhật trạng thái payment → `completed`
4. ✅ Kích hoạt subscription → `active`
5. ✅ Set thời gian hết hạn
6. ✅ Cấp credits cho brand

## Cấu hình

### Bước 1: Cấu hình .env

Copy từ `.env.example` và điền thông tin Sepay:

```env
# Sepay Payment Gateway Configuration
SEPAY_API_KEY=your-sepay-api-key-here
SEPAY_MERCHANT_ID=your-merchant-id-here
SEPAY_BANK_ACCOUNT_NUMBER=0123456789
SEPAY_BANK_NAME="MB Bank"
SEPAY_BANK_ACCOUNT_NAME="COMPANY NAME"
SEPAY_WEBHOOK_SECRET=your-webhook-secret-here
SEPAY_BASE_URL=https://my.sepay.vn/userapi
```

### Bước 2: Lấy thông tin từ Sepay

1. Đăng ký tài khoản tại: https://my.sepay.vn
2. Vào **Cài đặt** → **API Key** để lấy:
   - `SEPAY_API_KEY`
   - `SEPAY_MERCHANT_ID`
3. Cấu hình **Webhook URL**:
   ```
   https://yourdomain.com/webhook/sepay
   ```
4. Lấy **Webhook Secret** (nếu có)

### Bước 3: Cấu hình thông tin ngân hàng

Điền thông tin tài khoản ngân hàng nhận tiền:
- `SEPAY_BANK_ACCOUNT_NUMBER`: Số tài khoản
- `SEPAY_BANK_NAME`: Tên ngân hàng (MB Bank, Vietcombank, ...)
- `SEPAY_BANK_ACCOUNT_NAME`: Tên chủ tài khoản

## Testing

### Test 1: Tạo Payment và hiển thị QR Code

```bash
# 1. Chạy migrations
php artisan migrate

# 2. Seed data (nếu cần)
php artisan db:seed

# 3. Truy cập ứng dụng
# - Đăng nhập
# - Vào trang Brand
# - Chọn "Nâng cấp gói"
# - Chọn gói muốn mua
# - Xem QR Code thanh toán
```

**Kiểm tra:**
- ✅ QR Code hiển thị đúng
- ✅ Thông tin ngân hàng hiển thị
- ✅ Số tiền chính xác
- ✅ Nội dung chuyển khoản có format `BT0001000001`

### Test 2: Test Webhook với ngrok (Development)

#### Bước 1: Cài đặt ngrok

```bash
# Download và cài đặt ngrok
brew install ngrok  # macOS
# Hoặc tải từ: https://ngrok.com/download
```

#### Bước 2: Chạy Laravel server

```bash
php artisan serve
# Hoặc
php artisan serve --port=8000
```

#### Bước 3: Tạo tunnel với ngrok

```bash
ngrok http 8000
```

Ngrok sẽ cung cấp URL public, ví dụ:
```
https://abc123.ngrok.io
```

#### Bước 4: Cấu hình Webhook URL trong Sepay

Vào https://my.sepay.vn → Cài đặt → Webhook:
```
https://abc123.ngrok.io/webhook/sepay
```

#### Bước 5: Thử nghiệm chuyển khoản

1. Tạo payment trong ứng dụng
2. Lấy mã thanh toán (ví dụ: `BT0001000001`)
3. Chuyển khoản đúng số tiền với nội dung = mã thanh toán
4. Sepay sẽ gửi webhook đến server của bạn

#### Bước 6: Kiểm tra logs

```bash
# Xem logs Laravel
tail -f storage/logs/laravel.log | grep "Sepay"
```

**Logs mong đợi:**
```
[2024-01-01 12:00:00] local.INFO: Sepay webhook received
[2024-01-01 12:00:01] local.INFO: Sepay payment activated successfully
```

### Test 3: Test Webhook thủ công với cURL

Bạn có thể test webhook thủ công bằng cURL:

```bash
curl -X POST https://yourdomain.com/webhook/sepay \
  -H "Content-Type: application/json" \
  -d '{
    "id": "123456",
    "gateway": "MB",
    "transaction_date": "2024-01-01 12:00:00",
    "account_number": "0123456789",
    "amount_in": 100000,
    "amount_out": 0,
    "accumulated": 1000000,
    "transaction_content": "BT0001000001",
    "reference_number": "FT24001123456",
    "body": "Chuyen tien"
  }'
```

**Response mong đợi:**
```json
{
  "success": true,
  "message": "Payment processed successfully",
  "payment_id": 1
}
```

### Test 4: Test kiểm tra trạng thái Payment thủ công

```bash
# Vào trang payment details
# Click nút "Kiểm tra thanh toán"
# Hệ thống sẽ gọi Sepay API để check transaction
```

## Webhook Data Structure

Sepay sẽ gửi webhook với cấu trúc:

```json
{
  "id": "123456",
  "gateway": "MB",
  "transaction_date": "2024-01-01 12:00:00",
  "account_number": "0123456789",
  "sub_account": "",
  "amount_in": 100000,
  "amount_out": 0,
  "accumulated": 1000000,
  "code": "",
  "transaction_content": "BT0001000001",
  "reference_number": "FT24001123456",
  "body": "Chuyen tien tu NGUYEN VAN A"
}
```

**Các field quan trọng:**
- `amount_in`: Số tiền chuyển vào (VNĐ)
- `transaction_content`: Nội dung chuyển khoản (chứa mã payment)
- `reference_number`: Mã tham chiếu từ ngân hàng

## Routes

### User Routes
```php
// Tạo payment
POST /brands/{brand}/payments

// Xem payment details
GET /brands/{brand}/payments/{payment}

// Kiểm tra trạng thái (call Sepay API)
POST /brands/{brand}/payments/{payment}/check

// Lịch sử payments
GET /brands/{brand}/payments
```

### Webhook Route
```php
// Sepay webhook (không cần authentication)
POST /webhook/sepay
```

## Database Schema

### payments table

```php
- id: bigint (primary key)
- brand_id: bigint (foreign key)
- subscription_id: bigint (foreign key, nullable)
- amount: bigint (VNĐ)
- payment_method: string (default: 'sepay')
- transaction_id: string (mã payment: BT0001000001)
- sepay_reference: string (reference_number từ Sepay)
- status: enum (pending, completed, failed, refunded)
- paid_at: timestamp (nullable)
- metadata: json (nullable)
- created_at: timestamp
- updated_at: timestamp
```

## Troubleshooting

### Webhook không nhận được

1. **Kiểm tra Webhook URL trong Sepay:**
   - URL phải public (không localhost)
   - HTTPS (production)
   - Route đúng: `/webhook/sepay`

2. **Kiểm tra logs:**
   ```bash
   tail -f storage/logs/laravel.log | grep "Sepay"
   ```

3. **Test với ngrok:**
   - Đảm bảo ngrok đang chạy
   - URL ngrok chưa expire

### Payment không được kích hoạt

1. **Kiểm tra mã thanh toán:**
   - Format đúng: `BT0001000001`
   - Không có khoảng trắng
   - Viết hoa

2. **Kiểm tra số tiền:**
   - Số tiền chuyển khoản >= giá gói
   - `amount_in` trong webhook >= `payment->amount`

3. **Kiểm tra status:**
   - Payment phải ở trạng thái `pending`
   - Subscription phải ở trạng thái `pending`

### Signature verification failed

1. **Kiểm tra SEPAY_WEBHOOK_SECRET:**
   - Đúng với secret trong Sepay dashboard
   - Không có khoảng trắng thừa

2. **Tạm thời disable verification:**
   - Để trống `SEPAY_WEBHOOK_SECRET`
   - System sẽ skip verification

## Security Best Practices

### 1. Webhook Secret
**Luôn** cấu hình webhook secret để verify request từ Sepay:

```env
SEPAY_WEBHOOK_SECRET=your-strong-secret-here
```

### 2. HTTPS Only (Production)
Webhook URL **phải** dùng HTTPS trong production:
```
https://yourdomain.com/webhook/sepay  ✅
http://yourdomain.com/webhook/sepay   ❌
```

### 3. Rate Limiting
Webhook route đã được exclude khỏi CSRF protection nhưng nên thêm rate limiting:

```php
// routes/web.php
Route::post('/webhook/sepay', [SepayWebhookController::class, 'handle'])
    ->middleware('throttle:60,1')
    ->name('webhook.sepay');
```

### 4. Logging
Tất cả webhook requests đều được log để audit:
- Request headers
- Request data
- Processing results
- Errors

## Production Deployment

### Checklist

- [ ] Cấu hình đầy đủ environment variables
- [ ] Webhook URL sử dụng HTTPS
- [ ] Cấu hình webhook trong Sepay dashboard
- [ ] Test webhook với transaction thật
- [ ] Kiểm tra logs hoạt động bình thường
- [ ] Setup monitoring cho webhook failures
- [ ] Backup database trước khi deploy

### Monitoring

Theo dõi các metrics:
- Số webhook requests nhận được
- Tỷ lệ thành công/thất bại
- Thời gian xử lý webhook
- Payment activation rate

```bash
# Check webhook logs
grep "Sepay webhook" storage/logs/laravel.log | tail -100

# Check successful activations
grep "Sepay payment activated" storage/logs/laravel.log | wc -l

# Check errors
grep "Sepay webhook processing error" storage/logs/laravel.log
```

## Support

Nếu gặp vấn đề:

1. **Kiểm tra logs:** `storage/logs/laravel.log`
2. **Kiểm tra Sepay dashboard:** Transaction history
3. **Test với webhook test tool:** Postman, cURL
4. **Liên hệ Sepay support:** https://my.sepay.vn/support

## API Reference

### Sepay API Documentation
https://docs.sepay.vn

### VietQR Documentation
https://www.vietqr.io/

## Changelog

### v1.0.0 (2024-01-01)
- ✅ Tích hợp Sepay webhook
- ✅ Tự động kích hoạt subscription
- ✅ QR Code payment
- ✅ Manual payment check
- ✅ Logging & monitoring
