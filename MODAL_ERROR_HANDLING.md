# Modal Error Handling - Giữ Modal Mở Khi Có Validation Errors

## Vấn đề
Trước đây, khi submit form trong modal có validation errors:
- Modal bị đóng sau khi page reload
- Toast hiển thị error nhưng user phải mở lại modal để sửa
- Trải nghiệm người dùng không tốt

## Giải pháp
Modal sẽ tự động mở lại khi có validation errors, giữ nguyên dữ liệu đã nhập.

## Cách hoạt động

### 1. Track Modal Mode
Thêm hidden input vào form để Laravel biết modal nào đang được submit:
```blade
<input type="hidden" name="_brand_modal_mode" value="{{ $mode }}">
```

### 2. Auto-open Modal State
Khởi tạo modal state dựa trên validation errors và old input:

**Dashboard (Create mode):**
```blade
addBrandModal: {{ $errors->any() && old('_brand_modal_mode') === 'create' ? 'true' : 'false' }}
```

**Brand Show (Edit mode):**
```blade
editBrandModal: {{ $errors->any() && old('_brand_modal_mode') === 'edit' ? 'true' : 'false' }}
```

### 3. Preserve Input Data
Laravel's `old()` helper tự động giữ lại:
- Tất cả text inputs
- Select values
- Textarea content
- Checkbox/radio states

### 4. Error Display
Validation errors hiển thị trong modal với:
```blade
@error('field_name')
    <p class="tw-text-red-500 tw-text-sm tw-mt-1">{{ $message }}</p>
@enderror
```

## Files Modified

1. **modal-brand-form.blade.php**
   - Thêm hidden input `_brand_modal_mode`
   - Xóa script auto-open (không cần nữa)

2. **dashboard.blade.php**
   - Initial state: `addBrandModal` dựa trên errors và old input

3. **brands/show.blade.php**
   - Initial state: `editBrandModal` dựa trên errors và old input

## Flow hoạt động

```
1. User mở modal và điền form
2. Submit form với dữ liệu invalid
3. Server validate và redirect back với errors
4. Page reload
5. Alpine.js khởi tạo với modal state = true (nếu có errors)
6. Modal tự động hiển thị
7. Form data được restore từ old() helper
8. Errors hiển thị bên cạnh các fields
9. User sửa lỗi và submit lại
```

## Lợi ích

✅ Modal không bị đóng khi có validation errors
✅ Dữ liệu đã nhập được giữ nguyên
✅ User có thể sửa lỗi ngay mà không cần mở lại modal
✅ Trải nghiệm người dùng tốt hơn
✅ Không cần AJAX, giữ nguyên flow Laravel validation

---

## Áp dụng cho Member Management

Tính năng tương tự đã được áp dụng cho trang quản lý thành viên (brands/members/index.blade.php).

### Các modal được cập nhật:

#### 1. Invite Member Modal (Mời thành viên)
- **Hidden field**: `_modal_type = "invite"`
- **Auto-open**: Khi có errors và `old('_modal_type') === 'invite'`
- **Preserved data**:
  - Email input: `old('email')`
  - Role selection: `old('role', 'editor')`
  - Current role display state

#### 2. Edit Role Modal (Thay đổi vai trò)
- **Hidden fields**: 
  - `_modal_type = "edit"`
  - `_member_id` (Alpine binding)
- **Auto-open**: Khi có errors và `old('_modal_type') === 'edit'`
- **Preserved data**:
  - Member ID: `old('_member_id')`
  - Role selection: Kết hợp Alpine state và `old('role')`

### Initial State Setup

```blade
inviteModal: {{ $errors->any() && old('_modal_type') === 'invite' ? 'true' : 'false' }},
editModal: {{ $errors->any() && old('_modal_type') === 'edit' ? 'true' : 'false' }},
currentRole: '{{ old('role', 'editor') }}',
editMemberId: {{ old('_member_id', 'null') }},
editMemberRole: '{{ old('role', '') }}',
```

### Validation Error Display

**Invite Modal:**
```blade
@error('email')
    <span class="tw-text-red-500 tw-text-sm tw-mt-1">{{ $message }}</span>
@enderror
```

**Edit Modal:**
```blade
@error('role')
    <p class="tw-text-red-500 tw-text-sm tw-mt-2">{{ $message }}</p>
@enderror
```

### Radio Button State

Kết hợp Alpine.js state với Laravel old() helper:
```blade
:checked="editMemberRole === 'admin' || '{{ old('role') }}' === 'admin'"
```

## Tổng kết

✅ **Brand Management**: Create & Edit modals  
✅ **Member Management**: Invite & Edit Role modals

Tất cả modals giờ đều:
- Tự động mở lại khi có validation errors
- Giữ nguyên dữ liệu đã nhập
- Hiển thị error messages rõ ràng
- Không cần AJAX, flow Laravel validation thuần túy
