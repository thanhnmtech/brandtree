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
        
        // Chặn scroll body
        document.body.style.overflow = "hidden";

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
        // Restore scroll body
        document.body.style.overflow = "";
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
            const result = await this.sendSaveRequest();

            if (result.status === "success") {
                this.showStatus("Đã lưu thành công", "success");

                // Cập nhật local data
                const newData = { ...this.dataValue };
                newData[this.currentKey] = this.contentTarget.value;
                this.dataValue = newData;

                // Cập nhật các phần UI (Next Step, Progress, Steps)
                this.updateUIFromResponse(result);

                // Cập nhật trạng thái navigation dropdown
                this.updateNavigationDropdown();
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
     * Gửi request lưu dữ liệu lên server
     */
    async sendSaveRequest() {
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
        return await response.json();
    }

    /**
     * Cập nhật các phần UI sau khi lưu thành công
     */
    updateUIFromResponse(result) {
        // Nếu có next_step_html từ server (Trang Brand Show), update luôn
        if (result.next_step_html && this.hasNextStepContainerTarget) {
            this.nextStepContainerTarget.innerHTML = result.next_step_html;
        }

        // Nếu có progress_header_html từ server, update Progress Header cards
        if (result.progress_header_html && this.hasProgressContainerTarget) {
            this.progressContainerTarget.innerHTML = result.progress_header_html;
        }

        // Load lại danh sách bước (dùng cho trang Root/Trunk)
        this.fetchSteps();
    }

    /**
     * Cập nhật trạng thái navigation dropdown sau khi lưu
     * - Đổi style item hiện tại sang màu xanh
     * - Unlock item tiếp theo nếu có
     * - Xử lý đặc biệt khi hoàn thành root3 → unlock trunk1
     */
    updateNavigationDropdown() {
        // Tìm cả a và span với data-nav-key
        const navItem = document.querySelector(`[data-nav-key="${this.currentKey}"]`);
        if (!navItem) return;

        // Current Item: Đổi style sang màu xanh (unlocked)
        navItem.classList.remove('tw-text-[#7B7773]', 'tw-bg-[#e7e5df]', 'tw-cursor-not-allowed');
        navItem.classList.add('tw-text-vlbcgreen', 'tw-bg-[#F4FCF7]');

        // Tìm container chứa tất cả các navigation items
        const container = navItem.closest('.tw-rounded-\\[4px\\]');
        if (!container) return;

        // Lấy tất cả items trong container
        const allItems = container.querySelectorAll('[data-nav-key]');
        let currentIndex = -1;
        
        // Tìm index của item hiện tại
        allItems.forEach((item, index) => {
            if (item.getAttribute('data-nav-key') === this.currentKey) {
                currentIndex = index;
            }
        });

        // Unlock next item nếu có trong cùng dropdown
        if (currentIndex !== -1 && currentIndex + 1 < allItems.length) {
            const nextItem = allItems[currentIndex + 1];
            const nextKey = nextItem.getAttribute('data-nav-key');
            
            // Nếu next item là span (locked), thay bằng a (unlocked)
            if (nextItem.tagName === 'SPAN') {
                const newLink = document.createElement('a');
                newLink.href = `/brands/${this.brandSlugValue}/chat/${nextKey}`;
                newLink.setAttribute('data-nav-key', nextKey);
                newLink.className = nextItem.className.replace('tw-cursor-not-allowed', '');
                newLink.classList.remove('tw-text-[#7B7773]', 'tw-bg-[#e7e5df]');
                newLink.classList.add('tw-text-vlbcgreen', 'tw-bg-[#F4FCF7]', 'hover:tw-opacity-80');
                newLink.innerHTML = nextItem.innerHTML;
                nextItem.replaceWith(newLink);
            } else {
                // Nếu đã là a, chỉ cần đổi style
                nextItem.classList.remove('tw-text-[#7B7773]', 'tw-bg-[#e7e5df]');
                nextItem.classList.add('tw-text-vlbcgreen', 'tw-bg-[#F4FCF7]');
            }
        }
        
        // Nếu là step cuối của root (root3), kiểm tra và unlock trunk1
        if (this.currentKey === 'root3') {
            // Kiểm tra tất cả root steps đã có data chưa
            const rootKeys = ['root1', 'root2', 'root3'];
            const allRootDone = rootKeys.every(key => this.dataValue[key]);
            
            if (allRootDone) {
                // Tìm trunk1 trong dropdown trunk
                const trunk1Item = document.querySelector('[data-nav-key="trunk1"]');
                if (trunk1Item && trunk1Item.tagName === 'SPAN') {
                    const newLink = document.createElement('a');
                    newLink.href = `/brands/${this.brandSlugValue}/chat/trunk1`;
                    newLink.setAttribute('data-nav-key', 'trunk1');
                    newLink.className = trunk1Item.className.replace('tw-cursor-not-allowed', '');
                    newLink.classList.remove('tw-text-[#7B7773]', 'tw-bg-[#e7e5df]');
                    newLink.classList.add('tw-text-vlbcgreen', 'tw-bg-[#F4FCF7]', 'hover:tw-opacity-80');
                    newLink.innerHTML = trunk1Item.innerHTML;
                    trunk1Item.replaceWith(newLink);
                }
            }
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
