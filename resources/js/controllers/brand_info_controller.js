import { Controller } from "@hotwired/stimulus";

/**
 * Brand Info Controller
 * Điều khiển modal hiển thị thông tin thương hiệu (read-only)
 * Dữ liệu từ root_data và trunk_data trong database
 */
export default class extends Controller {
    // Khai báo targets cho modal và các content div
    static targets = [
        "modal_brand_info",
        "root1Content",
        "root2Content", 
        "root3Content",
        "trunk1Content",
        "trunk2Content"
    ];

    // Nhận dữ liệu từ Blade thông qua data-brand-info-data-value
    static values = {
        data: Object
    };

    /**
     * Mở modal và hiển thị dữ liệu
     */
    open() {
        // Cập nhật nội dung từ data value
        this.updateContent();
        // Hiển thị modal
        this.modal_brand_infoTarget.classList.remove("tw-hidden");
        // Ngăn scroll body
        document.body.style.overflow = "hidden";
    }

    /**
     * Đóng modal
     */
    close() {
        this.modal_brand_infoTarget.classList.add("tw-hidden");
        // Cho phép scroll body lại
        document.body.style.overflow = "";
    }

    /**
     * Đóng modal khi click vào backdrop (nền đen)
     */
    closeOnBackdrop(event) {
        // Chỉ đóng nếu click vào chính backdrop, không phải nội dung bên trong
        if (event.target === this.modal_brand_infoTarget) {
            this.close();
        }
    }

    /**
     * Cập nhật nội dung các field từ data value
     */
    updateContent() {
        const data = this.dataValue || {};
        
        // Cập nhật Root fields
        this.setContent(this.root1ContentTarget, data.root1);
        this.setContent(this.root2ContentTarget, data.root2);
        this.setContent(this.root3ContentTarget, data.root3);
        
        // Cập nhật Trunk fields
        this.setContent(this.trunk1ContentTarget, data.trunk1);
        this.setContent(this.trunk2ContentTarget, data.trunk2);
    }

    /**
     * Helper: Set nội dung cho một target element
     * @param {HTMLElement} target - Element cần cập nhật
     * @param {string} content - Nội dung text
     */
    setContent(target, content) {
        if (content && content.trim() !== "") {
            target.innerHTML = this.escapeHtml(content);
        } else {
            target.innerHTML = '<span class="tw-text-gray-400 tw-italic">Chưa có dữ liệu</span>';
        }
    }

    /**
     * Helper: Escape HTML để tránh XSS
     * @param {string} text - Text cần escape
     * @returns {string} - Text đã escape
     */
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML.replace(/\n/g, '<br>');
    }
}
