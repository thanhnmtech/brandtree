import { Controller } from "@hotwired/stimulus";

/**
 * Brand Info Controller
 * Điều khiển modal hiển thị thông tin thương hiệu với chức năng Inline Edit
 * Sử dụng contenteditable để chỉnh sửa trực tiếp trên nội dung
 * 
 * Cấu trúc tương tự result_modal_controller.js để dễ maintain
 */
export default class extends Controller {
    // ==================== TARGETS ====================
    static targets = [
        "modal_brand_info",
        // Content targets (hiển thị nội dung, có thể edit trực tiếp)
        "root1Content", "root2Content", "root3Content",
        "trunk1Content", "trunk2Content",
        // Edit button targets
        "root1EditBtn", "root2EditBtn", "root3EditBtn",
        "trunk1EditBtn", "trunk2EditBtn",
        // Save button targets
        "root1SaveBtn", "root2SaveBtn", "root3SaveBtn",
        "trunk1SaveBtn", "trunk2SaveBtn",
        // Cancel button targets
        "root1CancelBtn", "root2CancelBtn", "root3CancelBtn",
        "trunk1CancelBtn", "trunk2CancelBtn"
    ];

    // ==================== VALUES ====================
    static values = {
        data: { type: Object, default: {} },
        brandSlug: String
    };

    // ==================== STATE ====================
    // Lưu nội dung gốc khi bắt đầu edit (để hủy được)
    originalContent = {};
    // Trạng thái đang lưu
    isSaving = false;
    // Field đang được edit
    currentEditingField = null;

    // ==================== LIFECYCLE ====================
    
    connect() {
        console.log("BrandInfo: Controller connected");
        // Đóng modal khi nhấn Escape
        this.handleEscape = this.handleEscape.bind(this);
        document.addEventListener("keydown", this.handleEscape);
    }

    disconnect() {
        document.removeEventListener("keydown", this.handleEscape);
    }

    handleEscape(event) {
        if (event.key === "Escape" && this.isOpen()) {
            // Nếu đang edit, hủy edit trước
            if (this.currentEditingField) {
                this.exitEditMode(this.currentEditingField, false);
            } else {
                this.close();
            }
        }
    }

    // ==================== MODAL ACTIONS ====================

    /**
     * Mở modal và hiển thị dữ liệu
     */
    open() {
        console.log("BrandInfo: Opening modal");
        this.updateContent();
        this.modal_brand_infoTarget.classList.remove("tw-hidden");
        document.body.style.overflow = "hidden";
    }

    /**
     * Đóng modal
     */
    close() {
        console.log("BrandInfo: Closing modal");
        // Hủy tất cả edit đang mở trước khi đóng
        this.cancelAllEdits();
        this.modal_brand_infoTarget.classList.add("tw-hidden");
        document.body.style.overflow = "";
    }

    /**
     * Đóng modal khi click vào backdrop
     */
    closeOnBackdrop(event) {
        if (event.target === this.modal_brand_infoTarget) {
            this.close();
        }
    }

    /**
     * Kiểm tra modal có đang mở không
     */
    isOpen() {
        return !this.modal_brand_infoTarget.classList.contains("tw-hidden");
    }

    // ==================== CONTENT MANAGEMENT ====================

    /**
     * Cập nhật nội dung các field từ data value
     */
    updateContent() {
        const data = this.dataValue || {};
        console.log("BrandInfo: Updating content with data:", data);
        
        this.setContent(this.root1ContentTarget, data.root1);
        this.setContent(this.root2ContentTarget, data.root2);
        this.setContent(this.root3ContentTarget, data.root3);
        this.setContent(this.trunk1ContentTarget, data.trunk1);
        this.setContent(this.trunk2ContentTarget, data.trunk2);
    }

    /**
     * Set nội dung cho một target element
     */
    setContent(target, content) {
        if (content && content.trim() !== "") {
            target.innerHTML = this.escapeHtml(content);
        } else {
            target.innerHTML = '<span class="tw-text-gray-400 tw-italic">Chưa có dữ liệu</span>';
        }
    }

    /**
     * Escape HTML để tránh XSS
     */
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML.replace(/\n/g, '<br>');
    }

    // ==================== EDIT MODE ====================

    /**
     * Chuyển sang chế độ edit - bật contenteditable
     */
    toggleEdit(event) {
        const field = event.currentTarget.dataset.field;
        console.log("BrandInfo: Toggle edit for field:", field);
        
        const contentTarget = this[`${field}ContentTarget`];
        const editBtn = this[`${field}EditBtnTarget`];
        const saveBtn = this[`${field}SaveBtnTarget`];
        const cancelBtn = this[`${field}CancelBtnTarget`];
        
        // Lưu nội dung gốc (text thuần) để có thể hủy
        const currentData = this.dataValue || {};
        this.originalContent[field] = currentData[field] || '';
        this.currentEditingField = field;
        
        // Điền nội dung text thuần vào div (không có HTML escape)
        contentTarget.innerText = this.originalContent[field];
        
        // Bật chế độ edit
        contentTarget.setAttribute('contenteditable', 'true');
        contentTarget.focus();
        
        // Đặt cursor ở cuối nội dung
        this.placeCursorAtEnd(contentTarget);
        
        // Toggle buttons visibility
        this.toggleEditButtons(field, true);
    }

    /**
     * Hủy chỉnh sửa
     */
    cancelEdit(event) {
        const field = event.currentTarget.dataset.field;
        console.log("BrandInfo: Cancel edit for field:", field);
        this.exitEditMode(field, false);
    }

    /**
     * Toggle hiển thị các buttons Edit/Save/Cancel
     */
    toggleEditButtons(field, isEditing) {
        const editBtn = this[`${field}EditBtnTarget`];
        const saveBtn = this[`${field}SaveBtnTarget`];
        const cancelBtn = this[`${field}CancelBtnTarget`];
        
        if (isEditing) {
            editBtn.classList.add('tw-hidden');
            saveBtn.classList.remove('tw-hidden');
            cancelBtn.classList.remove('tw-hidden');
        } else {
            editBtn.classList.remove('tw-hidden');
            saveBtn.classList.add('tw-hidden');
            cancelBtn.classList.add('tw-hidden');
        }
    }

    /**
     * Thoát chế độ edit
     * @param {string} field - Tên field
     * @param {boolean} saved - Đã lưu thành công hay chưa
     * @param {string} newContent - Nội dung mới (nếu đã lưu)
     */
    exitEditMode(field, saved = false, newContent = null) {
        console.log("BrandInfo: Exit edit mode for field:", field, "saved:", saved);
        
        const contentTarget = this[`${field}ContentTarget`];
        
        // Tắt contenteditable
        contentTarget.removeAttribute('contenteditable');
        
        // Cập nhật nội dung hiển thị
        if (saved && newContent !== null) {
            this.setContent(contentTarget, newContent);
        } else {
            // Khôi phục nội dung gốc
            this.setContent(contentTarget, this.originalContent[field]);
        }
        
        // Toggle buttons
        this.toggleEditButtons(field, false);
        
        // Reset state
        this.currentEditingField = null;
    }

    /**
     * Hủy tất cả các edit đang mở
     */
    cancelAllEdits() {
        const fields = ['root1', 'root2', 'root3', 'trunk1', 'trunk2'];
        fields.forEach(field => {
            try {
                const contentTarget = this[`${field}ContentTarget`];
                if (contentTarget.hasAttribute('contenteditable')) {
                    this.exitEditMode(field, false);
                }
            } catch (e) {
                // Bỏ qua nếu target không tồn tại
            }
        });
    }

    // ==================== SAVE FUNCTIONALITY ====================

    /**
     * Lưu nội dung đã chỉnh sửa
     */
    async saveEdit(event) {
        if (this.isSaving) return;
        
        const button = event.currentTarget;
        const field = button.dataset.field;
        const contentTarget = this[`${field}ContentTarget`];
        
        // Lấy nội dung text thuần từ contenteditable
        const newContent = contentTarget.innerText.trim();
        
        console.log("BrandInfo: Saving field:", field, "content length:", newContent.length);
        
        const brandSlug = this.getBrandSlug();
        if (!brandSlug) {
            this.showToast('Không tìm thấy thông tin thương hiệu', 'error');
            return;
        }
        
        this.isSaving = true;
        this.updateSaveButtonState(button, true);
        
        try {
            const result = await this.sendSaveRequest(brandSlug, field, newContent);
            
            if (result.status === 'success') {
                // Cập nhật local data
                this.updateLocalData(field, newContent);
                
                // Thoát edit mode và cập nhật hiển thị
                this.exitEditMode(field, true, newContent);
                
                // Cập nhật UI bên ngoài
                this.updateUIFromResponse(result);
                
                this.showToast('Đã lưu thành công!', 'success');
            } else {
                throw new Error(result.message || 'Có lỗi xảy ra');
            }
        } catch (error) {
            console.error('BrandInfo: Save error:', error);
            this.showToast(error.message || 'Có lỗi xảy ra khi lưu', 'error');
        } finally {
            this.isSaving = false;
            this.updateSaveButtonState(button, false);
        }
    }

    /**
     * Gửi request lưu dữ liệu lên server
     */
    async sendSaveRequest(brandSlug, key, content) {
        console.log("BrandInfo: Sending save request to:", `/brands/${brandSlug}/update-section`);
        
        const response = await fetch(`/brands/${brandSlug}/update-section`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                key: key,
                content: content
            })
        });
        
        return await response.json();
    }

    /**
     * Cập nhật local data sau khi lưu thành công
     */
    updateLocalData(field, newContent) {
        const newData = { ...this.dataValue };
        newData[field] = newContent;
        this.dataValue = newData;
        console.log("BrandInfo: Local data updated for field:", field);
    }

    /**
     * Cập nhật trạng thái nút Save
     */
    updateSaveButtonState(button, loading) {
        if (loading) {
            button.innerHTML = '<i class="ri-loader-4-line tw-animate-spin tw-text-lg"></i>';
            button.disabled = true;
        } else {
            button.innerHTML = '<i class="ri-check-line tw-text-lg"></i><span>Lưu</span>';
            button.disabled = false;
        }
    }

    // ==================== UI UPDATES ====================

    /**
     * Cập nhật UI bên ngoài modal (progress header, next step)
     */
    updateUIFromResponse(result) {
        // Cập nhật Progress Header
        if (result.progress_header_html) {
            const progressContainer = document.querySelector('[data-result-modal-target="progressContainer"]');
            if (progressContainer) {
                progressContainer.innerHTML = result.progress_header_html;
                console.log("BrandInfo: Progress header updated");
            }
        }
        
        // Cập nhật Next Step
        if (result.next_step_html) {
            const nextStepContainer = document.querySelector('[data-result-modal-target="nextStepContainer"]');
            if (nextStepContainer) {
                nextStepContainer.innerHTML = result.next_step_html;
                console.log("BrandInfo: Next step updated");
            }
        }
    }

    // ==================== HELPERS ====================

    /**
     * Đặt cursor ở cuối nội dung
     */
    placeCursorAtEnd(element) {
        const range = document.createRange();
        const selection = window.getSelection();
        range.selectNodeContents(element);
        range.collapse(false);
        selection.removeAllRanges();
        selection.addRange(range);
    }

    /**
     * Lấy brand slug từ URL hoặc data value
     */
    getBrandSlug() {
        // Thử lấy từ data value trước
        if (this.hasBrandSlugValue && this.brandSlugValue) {
            return this.brandSlugValue;
        }
        
        // Fallback: lấy từ URL
        const pathParts = window.location.pathname.split('/');
        const brandsIndex = pathParts.indexOf('brands');
        if (brandsIndex !== -1 && pathParts[brandsIndex + 1]) {
            return pathParts[brandsIndex + 1];
        }
        
        console.warn("BrandInfo: Could not determine brand slug");
        return null;
    }

    /**
     * Hiển thị toast notification
     */
    showToast(message, type = 'success') {
        console.log("BrandInfo: Toast:", type, message);
        
        const toast = document.createElement('div');
        toast.className = `tw-fixed tw-bottom-4 tw-right-4 tw-z-[100] tw-px-6 tw-py-3 tw-rounded-lg tw-shadow-lg tw-flex tw-items-center tw-gap-2 tw-transition-all tw-duration-300 ${
            type === 'success' 
                ? 'tw-bg-[#1AA24C] tw-text-white' 
                : 'tw-bg-red-500 tw-text-white'
        }`;
        
        const icon = type === 'success' ? 'ri-check-line' : 'ri-error-warning-line';
        toast.innerHTML = `<i class="${icon}"></i> ${message}`;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
}
