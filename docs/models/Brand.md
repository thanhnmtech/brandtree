# Brand Model

Model quản lý thông tin thương hiệu và các chức năng liên quan đến credits, subscription.

## Attributes

| Attribute | Type | Mô tả |
|-----------|------|-------|
| `name` | string | Tên thương hiệu |
| `slug` | string | Slug URL (tự động tạo) |
| `industry` | string | Ngành nghề |
| `target_market` | string | Thị trường mục tiêu |
| `founded_year` | int | Năm thành lập |
| `description` | string | Mô tả |
| `logo_path` | string | Đường dẫn logo |
| `created_by` | int | ID user tạo brand |

## Computed Attributes

```php
$brand->logo_url;           // URL đầy đủ của logo
$brand->credits_remaining;  // Số credits còn lại
$brand->total_credits;      // Tổng credits từ plan
$brand->credit_usage_percent; // Phần trăm đã sử dụng
$brand->current_plan;       // Plan hiện tại (object)
$brand->current_plan_name;  // Tên plan hiện tại
```

## Relationships

```php
$brand->owner;              // User tạo brand
$brand->members;            // Danh sách BrandMember
$brand->users;              // Danh sách User là member
$brand->subscriptions;      // Tất cả subscriptions
$brand->activeSubscription; // Subscription đang active
$brand->creditUsages;       // Lịch sử sử dụng credits
$brand->payments;           // Lịch sử thanh toán
$brand->admins;             // Danh sách admin members
```

## Methods

### Kiểm tra quyền

```php
// Kiểm tra user có phải admin không
$brand->isAdmin($user); // bool

// Kiểm tra user có phải member không
$brand->isMember($user); // bool
```

### Credit Methods

```php
// Kiểm tra có subscription active không
$brand->hasActiveSubscription(); // bool

// Kiểm tra có đủ credits không
$brand->hasCredits(10); // bool - kiểm tra có >= 10 credits
$brand->hasCredits();   // bool - kiểm tra có >= 1 credit

// Sử dụng credits (yêu cầu user đã đăng nhập)
$brand->useCredits(5); // bool - trừ 5 credits, log action_type = 'chat'

// Thêm bonus credits
$brand->addCredits(100, 'Khuyến mãi tháng 1'); // bool
$brand->addCredits(50, 'Bonus', $specificUser); // bool - chỉ định user

// Lấy thống kê sử dụng credits
$brand->getCreditStats('month'); // array - thống kê theo tháng
$brand->getCreditStats('week');  // array - thống kê theo tuần
$brand->getCreditStats('year');  // array - thống kê theo năm

// Lấy dữ liệu sử dụng theo ngày (cho charts)
$brand->getDailyCreditsUsage('month'); // array
```

### Subscription Methods

```php
// Mua plan mới
$brand->purchasePlan($plan); // bool|BrandSubscription

// Gia hạn subscription hiện tại
$brand->renewSubscription(); // ?BrandSubscription

// Đổi sang plan khác
$brand->changePlan($newPlan); // ?BrandSubscription

// Kiểm tra có thể mua thêm gói credits không
$brand->canBuyCreditPackage(); // bool
```

## Ví dụ sử dụng

### Kiểm tra và sử dụng credits

```php
// Trong controller, user đã đăng nhập
$brand = Brand::find(1);

// Kiểm tra trước khi thực hiện action
if (!$brand->hasCredits(5)) {
    return response()->json(['error' => 'Không đủ credits'], 400);
}

// Sử dụng credits
if ($brand->useCredits(5)) {
    // Thực hiện action (chat, generate image, etc.)
} else {
    // Xử lý lỗi
}
```

### Hiển thị thông tin credits

```php
$brand = Brand::find(1);

echo "Plan: " . $brand->current_plan_name;
echo "Credits còn lại: " . $brand->credits_remaining . "/" . $brand->total_credits;
echo "Đã sử dụng: " . $brand->credit_usage_percent . "%";
```

### Lấy thống kê cho dashboard

```php
$brand = Brand::find(1);

// Thống kê tổng quan
$stats = $brand->getCreditStats('month');
// Returns: ['total_used' => 150, 'by_action' => [...], 'by_model' => [...]]

// Dữ liệu cho chart
$dailyUsage = $brand->getDailyCreditsUsage('month');
// Returns: ['2024-01-01' => 10, '2024-01-02' => 5, ...]
```

### Quản lý subscription

```php
$brand = Brand::find(1);
$plan = Plan::find(2);

// Mua plan mới
$subscription = $brand->purchasePlan($plan);

// Gia hạn
$brand->renewSubscription();

// Nâng cấp plan
$premiumPlan = Plan::where('slug', 'premium')->first();
$brand->changePlan($premiumPlan);
```

## Lưu ý

1. **useCredits()** yêu cầu user phải đăng nhập (`auth()->user()` không null)
2. **useCredits()** tự động log với `action_type = 'chat'`
3. Slug được tự động tạo khi create và update (nếu name thay đổi)
4. Route model binding sử dụng `slug` thay vì `id`
