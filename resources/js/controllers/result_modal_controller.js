import { Controller } from "@hotwired/stimulus";

/**
 * Result Modal Controller
 * Xử lý popup hiển thị kết quả phân tích cho các step (G1, G2, G3, T1, T2)
 * Có thể tái sử dụng ở nhiều trang: root, trunk, canopy, brand-info
 */
export default class extends Controller {
    // Khai báo các phần tử DOM cần thao tác
    static targets = [
        "modal", // Container modal
        "title", // Tiêu đề modal
        "content", // Textarea nội dung
        "status", // Thông báo trạng thái (success/error)
        "saveBtn", // Nút lưu
        "chatLink", // Link chat với AI
        "stepsContainer",
        "progressContainer",
        "nextStepContainer",
    ];

    // Khai báo các giá trị từ data attributes
    static values = {
        brandSlug: String, // Slug của brand
        url: String, // URL để reload data (vd: /brands/{slug}/root hoặc /trunk)
        data: { type: Object, default: {} }, // Object chứa dữ liệu các section
    };

    // State nội bộ
    currentKey = "";
    currentTitle = "";
    isSaving = false;

    connect() {
        // Đóng modal khi nhấn Escape
        this.handleEscape = this.handleEscape.bind(this);
        document.addEventListener("keydown", this.handleEscape);
    }

    disconnect() {
        document.removeEventListener("keydown", this.handleEscape);
    }

    handleEscape(event) {
        if (event.key === "Escape" && this.isOpen()) {
            this.close();
        }
    }

    /**
     * Mở modal với title và key từ params
     * Gọi từ button: data-action="result-modal#open"
     *                data-result-modal-title-param="Văn Hóa Dịch Vụ"
     *                data-result-modal-key-param="root1"
     */
    open(event) {
        const { title, key } = event.params;

        this.currentKey = key;
        this.currentTitle = title;

        // Cập nhật UI
        this.titleTarget.textContent = title;
        this.contentTarget.value = this.dataValue[key] || "";
        this.chatLinkTarget.href = this.getChatUrl();
        this.clearStatus();

        // Hiển thị modal
        this.modalTarget.classList.remove("tw-hidden");
        this.modalTarget.style.display = "";

        // Focus vào textarea
        setTimeout(() => this.contentTarget.focus(), 100);
    }

    /**
     * Đóng modal
     */
    close() {
        this.modalTarget.classList.add("tw-hidden");
        this.currentKey = "";
        this.currentTitle = "";
    }

    /**
     * Đóng modal khi click vào backdrop
     */
    closeOnBackdrop(event) {
        if (event.target === this.modalTarget) {
            this.close();
        }
    }

    /**
     * Kiểm tra modal có đang mở không
     */
    isOpen() {
        return !this.modalTarget.classList.contains("tw-hidden");
    }

    /**
     * Lấy URL chat với AI
     */
    getChatUrl() {
        return `/brands/${this.brandSlugValue}/chat/${this.currentKey}/1/new`;
    }

    /**
     * Lưu nội dung qua API
     */
    async save() {
        if (this.isSaving) return;

        this.isSaving = true;
        this.updateSaveButtonState(true);
        this.clearStatus();

        try {
            const response = await fetch(
                `/brands/${this.brandSlugValue}/update-section`,
                {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector(
                            'meta[name="csrf-token"]',
                        ).content,
                    },
                    body: JSON.stringify({
                        key: this.currentKey,
                        content: this.contentTarget.value,
                    }),
                },
            );

            const result = await response.json();

            if (result.status === "success") {
                this.showStatus("Đã lưu thành công", "success");

                // Cập nhật local data
                const newData = { ...this.dataValue };
                newData[this.currentKey] = this.contentTarget.value;
                this.dataValue = newData;

                // Load lại danh sách bước
                this.fetchSteps();

                // Update navigation dropdown status
                const navItem = document.querySelector(`a[data-nav-key="${this.currentKey}"]`);
                if (navItem) {
                    // Current Item: Ensure it has Green style (it might already have it if it was ready)
                    navItem.classList.remove('tw-text-[#7B7773]', 'tw-bg-[#e7e5df]');
                    navItem.classList.add('tw-text-vlbcgreen', 'tw-bg-[#F4FCF7]');

                    // Next Item: Unlocking
                    // Simple logic: Find next sibling a tag
                    let nextItem = navItem.nextElementSibling;
                    if (nextItem && nextItem.tagName === 'A') {
                        nextItem.classList.remove('tw-text-[#7B7773]', 'tw-bg-[#e7e5df]');
                        nextItem.classList.add('tw-text-vlbcgreen', 'tw-bg-[#F4FCF7]');
                    }
                }
            } else {
                this.showStatus(
                    "Lỗi: " + (result.message || "Không thể lưu"),
                    "error",
                );
            }
        } catch (error) {
            console.error("Save error:", error);
            this.showStatus("Lỗi kết nối", "error");
        } finally {
            this.isSaving = false;
            this.updateSaveButtonState(false);
        }
    }

    /**
     * Cập nhật trạng thái nút Lưu
     */
    updateSaveButtonState(loading) {
        if (loading) {
            this.saveBtnTarget.disabled = true;
            this.saveBtnTarget.innerHTML = `
                <span class="tw-animate-spin"><i class="ri-loader-4-line"></i></span>
                <span>Đang lưu...</span>
            `;
        } else {
            this.saveBtnTarget.disabled = false;
            this.saveBtnTarget.innerHTML = `<span>Lưu</span>`;
        }
    }

    /**
     * Hiển thị thông báo trạng thái
     */
    showStatus(message, type) {
        this.statusTarget.textContent = message;
        this.statusTarget.classList.remove(
            "tw-hidden",
            "tw-text-red-600",
            "tw-text-[#1AA24C]",
        );

        if (type === "error") {
            this.statusTarget.classList.add("tw-text-red-600");
        } else {
            this.statusTarget.classList.add("tw-text-[#1AA24C]");
        }
    }

    /**
     * Xóa thông báo trạng thái
     */
    async fetchSteps() {
        if (!this.hasStepsContainerTarget) return;

        try {
            this.stepsContainerTarget.classList.add("tw-opacity-50");

            const url = this.hasUrlValue 
                ? this.urlValue 
                : `/brands/${this.brandSlugValue}/root`;
            const response = await fetch(url, {
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "Accept": "application/json",
                },
            });

            if (response.ok) {
                const data = await response.json();
                if (data.html && this.hasStepsContainerTarget) {
                    this.stepsContainerTarget.innerHTML = data.html;
                }
                if (data.progress_html && this.hasProgressContainerTarget) {
                    this.progressContainerTarget.innerHTML = data.progress_html;
                }
                if (data.next_step_html && this.hasNextStepContainerTarget) {
                    this.nextStepContainerTarget.innerHTML = data.next_step_html;
                }
            }
        } catch (error) {
            console.error("ResultModal: Error fetching steps", error);
        } finally {
            this.stepsContainerTarget.classList.remove("tw-opacity-50");
        }
    }

    clearStatus() {
        this.statusTarget.textContent = "";
        this.statusTarget.classList.add("tw-hidden");
    }
}
