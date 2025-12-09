# Breadcrumb Component

Component Breadcrumb có thể tái sử dụng cho toàn bộ ứng dụng.

## Cách sử dụng

### Cú pháp cơ bản

```blade
<x-breadcrumb :items="[
    ['label' => 'Trang chủ', 'url' => route('dashboard')],
    ['label' => 'Thương hiệu', 'url' => route('brands.index')],
    ['label' => 'Chi tiết']
]" />
```

### Tham số

#### `items` (array, required)

Mảng các items breadcrumb. Mỗi item là một array với các key:

- **`label`** (string, required): Tên hiển thị của breadcrumb item
- **`url`** (string, optional): URL để navigate. Nếu không có sẽ hiển thị dạng text

### Quy tắc

1. **Item cuối cùng** luôn hiển thị dạng text (không clickable) và có màu đậm hơn
2. **Các item còn lại** nếu có `url` sẽ hiển thị dạng link
3. **Icon phân cách** (chevron right) tự động thêm giữa các items

## Ví dụ sử dụng

### 1. Breadcrumb đơn giản (2 levels)

```blade
<x-breadcrumb :items="[
    ['label' => 'Trang chủ', 'url' => route('dashboard')],
    ['label' => 'Cài đặt']
]" />
```

**Output:**
```
Trang chủ > Cài đặt
```

### 2. Breadcrumb nhiều cấp với dynamic data

```blade
<x-breadcrumb :items="[
    ['label' => 'Trang chủ', 'url' => route('dashboard')],
    ['label' => $brand->name, 'url' => route('brands.show', $brand)],
    ['label' => 'Quản lý Thành viên']
]" />
```

**Output:**
```
Trang chủ > Brand ABC > Quản lý Thành viên
```

### 3. Breadcrumb phức tạp (4+ levels)

```blade
<x-breadcrumb :items="[
    ['label' => 'Dashboard', 'url' => route('dashboard')],
    ['label' => 'Thương hiệu', 'url' => route('brands.index')],
    ['label' => $brand->name, 'url' => route('brands.show', $brand)],
    ['label' => 'Gói dịch vụ', 'url' => route('brands.subscription.show', $brand)],
    ['label' => 'Thanh toán']
]" />
```

### 4. Breadcrumb không có link (chỉ text)

```blade
<x-breadcrumb :items="[
    ['label' => 'Báo cáo'],
    ['label' => 'Thống kê hàng tháng']
]" />
```

## Styling

Component sử dụng Tailwind CSS với prefix `tw-`:

- Responsive: Spacing tự động điều chỉnh trên mobile (`md:tw-space-x-3`)
- Colors:
  - Links: `tw-text-gray-500` hover `tw-text-gray-700`
  - Current page: `tw-text-gray-700 tw-font-medium`
  - Separator icon: `tw-text-gray-400`

## Accessibility

- Sử dụng semantic HTML `<nav>` với `aria-label="Breadcrumb"`
- Structure `<ol>` và `<li>` cho screen readers
- Current page item có styling đặc biệt để phân biệt

## Các file đã sử dụng

1. `resources/views/brands/members/index.blade.php`
2. `resources/views/brands/subscription/show.blade.php`
3. `resources/views/brands/payments/create.blade.php`
4. `resources/views/brands/credits/statistics.blade.php`

## Lưu ý

- Component tự động thêm separator icon giữa các items
- Item cuối cùng luôn không clickable (best practice cho breadcrumb)
- Nếu muốn item cuối clickable, thêm thêm 1 item nữa
