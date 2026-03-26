import { Controller } from "@hotwired/stimulus";

/**
 * Result Modal Controller
 * Xử lý popup hiển thị kết quả phân tích cho các step (G1, G2, G3, T1, T2)
 * Có thể tái sử dụng ở nhiều trang: root, trunk, canopy, brand-info
 */
export default class extends Controller {
    // Khai báo các phần tử DOM cần thao tác
    static targets = [
        "modal",            // Container modal
        "title",            // Tiêu đề modal
        "content",          // Textarea nội dung (chế độ Sửa)
        "preview",          // Container chế độ Xem
        "previewContent",   // Div chứa HTML markdown đã render
        "viewTab",          // Tab "Xem"
        "editTab",          // Tab "Sửa"
        "status",           // Thông báo trạng thái (success/error)
        "saveBtn",          // Nút lưu
        "chatLink",         // Link chat với AI
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
    isViewMode = true; // Mặc định mở ở chế độ Xem
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

        // Render markdown và mặc định mở chế độ Xem
        this.renderMarkdown();
        this.switchToView();

        // Hiển thị modal
        this.modalTarget.classList.remove("tw-hidden");
        this.modalTarget.style.display = "";

        // Chặn scroll body
        document.body.style.overflow = "hidden";
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
     * Chuyển sang chế độ Xem (Preview markdown)
     * Re-render markdown từ nội dung textarea hiện tại
     */
    switchToView() {
        this.isViewMode = true;

        // Re-render markdown từ textarea (có thể đã được sửa)
        this.renderMarkdown();

        // Hiện preview, ẩn textarea
        this.previewTarget.classList.remove("tw-hidden");
        this.contentTarget.classList.add("tw-hidden");

        // Cập nhật style tabs
        this.viewTabTarget.classList.add("tw-border-[#1AA24C]", "tw-text-[#1AA24C]");
        this.viewTabTarget.classList.remove("tw-border-transparent", "tw-text-gray-500");
        this.editTabTarget.classList.add("tw-border-transparent", "tw-text-gray-500");
        this.editTabTarget.classList.remove("tw-border-[#1AA24C]", "tw-text-[#1AA24C]");
    }

    /**
     * Chuyển sang chế độ Sửa (Textarea raw text)
     */
    switchToEdit() {
        this.isViewMode = false;

        // Ẩn preview, hiện textarea
        this.previewTarget.classList.add("tw-hidden");
        this.contentTarget.classList.remove("tw-hidden");

        // Cập nhật style tabs
        this.editTabTarget.classList.add("tw-border-[#1AA24C]", "tw-text-[#1AA24C]");
        this.editTabTarget.classList.remove("tw-border-transparent", "tw-text-gray-500");
        this.viewTabTarget.classList.add("tw-border-transparent", "tw-text-gray-500");
        this.viewTabTarget.classList.remove("tw-border-[#1AA24C]", "tw-text-[#1AA24C]");

        // Focus vào textarea
        setTimeout(() => this.contentTarget.focus(), 100);
    }

    /**
     * Parse markdown content bằng marked.js và render vào preview
     * Bọc <table> trong div.table-wrapper để hỗ trợ cuộn ngang
     */
    renderMarkdown() {
        const rawContent = this.contentTarget.value || "";

        if (!rawContent.trim()) {
            this.previewContentTarget.innerHTML =
                '<p class="tw-text-gray-400 tw-italic">Chưa có kết quả phân tích...</p>';
            return;
        }

        try {
            // Parse markdown bằng marked.js (GFM: table, strikethrough, task list...)
            let html = marked.marked(rawContent, {
                breaks: true, // Xuống dòng = <br>
                gfm: true, // GitHub Flavored Markdown
            });

            // Bọc <table> trong div.table-wrapper để cuộn ngang khi bảng quá rộng
            html = html
                .replace(/<table>/g, '<div class="table-wrapper"><table>')
                .replace(/<\/table>/g, "</table></div>");

            this.previewContentTarget.innerHTML = html;
        } catch (e) {
            console.warn("Marked parse error, fallback to plain text:", e);
            // Fallback: escape HTML và thay \n bằng <br>
            this.previewContentTarget.innerHTML = rawContent
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/\n/g, "<br>");
        }
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

                // Gửi event để chat-result-bar xử lý polling (hoặc clear data khi empty)
                window.dispatchEvent(new CustomEvent('analysis-saved', {
                    detail: {
                        agentType: this.currentKey,
                        content: this.contentTarget.value
                    }
                }));

                // Cập nhật các phần UI (Next Step, Progress, Steps)
                this.updateUIFromResponse(result);

                // Cập nhật trạng thái navigation dropdown
                this.updateNavigationDropdown();
            } else {
                this.showStatus(
                    "Có lỗi xảy ra khi lưu",
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

    // Note: hardcode type = data trong request để lưu vào cột root_data/trunk_data. Nếu muốn lưu vào brief_data, có thể truyền thêm data-result-modal-type-value="brief" từ button và sử dụng ở đây
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
                    type: this.typeValue || "data", // Có thể mở rộng nếu muốn phân biệt brief/data
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
        if (!this.contentTarget.value || this.contentTarget.value.trim() === '') return;
        
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
