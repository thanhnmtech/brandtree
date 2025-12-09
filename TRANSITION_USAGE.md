# X-Transition Component Usage Guide

## Component: `<x-modal-transition>`

Component này cung cấp x-transition attributes nhất quán cho toàn bộ ứng dụng.

### Các loại transition có sẵn:

1. **overlay** - Cho backdrop/overlay của modal
   - Duration: 200ms enter, 150ms leave
   - Effect: Fade in/out (opacity only)

2. **modal** - Cho modal content
   - Duration: 200ms enter, 150ms leave
   - Effect: Fade + Scale (95% → 100%)

3. **dropdown** - Cho dropdown menu
   - Duration: 100ms enter, 75ms leave
   - Effect: Fade + Scale (95% → 100%)

4. **fade** - Cho content transitions đơn giản
   - Duration: 200ms enter, 150ms leave
   - Effect: Fade in/out (opacity only)

### Cách sử dụng:

#### Modal Popup
```blade
<x-modal-transition type="overlay" x-show="showModal" class="...">
    <x-modal-transition type="modal" class="...">
        <!-- Modal content -->
    </x-modal-transition>
</x-modal-transition>
```

#### Dropdown Menu
```blade
<x-modal-transition type="dropdown" x-show="open" class="...">
    <!-- Dropdown items -->
</x-modal-transition>
```

#### Fade Content
```blade
<x-modal-transition type="fade" x-show="isVisible" class="...">
    <!-- Content -->
</x-modal-transition>
```

### Lợi ích:

- ✅ Tất cả transitions nhất quán trong toàn bộ app
- ✅ Dễ dàng thay đổi transition behavior tại một chỗ duy nhất
- ✅ Code sạch hơn, không phải lặp lại x-transition attributes
- ✅ Dễ maintain và scale

### Files đã được cập nhật (100%):

#### Modal Components
- ✅ `resources/views/components/modal-brand-form.blade.php` - Brand form modal
- ✅ `resources/views/components/modal.blade.php` - Laravel Breeze modal component

#### Page Views
- ✅ `resources/views/brands/show.blade.php` - Dropdown menu
- ✅ `resources/views/brands/members/index.blade.php` - Invite, Edit, Delete modals
- ✅ `resources/views/plans/index.blade.php` - Plan tabs fade transition

#### Reusable Components
- ✅ `resources/views/components/dropdown.blade.php` - Dropdown component
- ✅ `resources/views/dashboard.blade.php` - Add brand modal (via component)

### Thống kê:

- **Tổng số files cập nhật**: 7 files
- **Tổng số modal/dropdown**: 8 transitions
- **Loại transition được dùng**:
  - `overlay`: 4 lần (modal backdrops)
  - `modal`: 4 lần (modal content)
  - `dropdown`: 2 lần (dropdown menus)
  - `fade`: 2 lần (content transitions)

---

**Ghi chú**: Tất cả x-transition trong dự án đã được thống nhất sử dụng `<x-modal-transition>` component. Không còn inline x-transition nào nữa!
