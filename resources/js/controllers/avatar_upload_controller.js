import { Controller } from "@hotwired/stimulus"

/**
 * Avatar Upload Controller
 * Xử lý popup upload, preview và xóa avatar người dùng
 * 
 * Targets:
 * - popup: Container popup chính
 * - previewImg: Thẻ img hiển thị preview
 * - placeholder: Placeholder khi chưa có avatar
 * - fileInput: Input file ẩn
 * - confirmBtn: Nút xác nhận upload
 * - deleteBtn: Nút xóa avatar
 * 
 * Values:
 * - currentAvatar: URL avatar hiện tại
 * - deleteUrl: URL để xóa avatar
 * - csrfToken: CSRF token
 */
export default class extends Controller {
    static targets = ["popup", "previewImg", "placeholder", "fileInput", "confirmBtn", "deleteBtn"]
    static values = {
        currentAvatar: String,
        deleteUrl: String,
        csrfToken: String
    }

    // Mở popup
    open() {
        this.popupTarget.classList.remove("tw-hidden")
        // Chặn scroll body
        document.body.style.overflow = "hidden"
        // Đóng account popup nếu có
        const accountPopup = document.getElementById("accountPopup")
        if (accountPopup) {
            accountPopup.classList.add("tw-hidden")
        }
    }

    // Đóng popup
    close() {
        this.popupTarget.classList.add("tw-hidden")
        // Restore scroll body
        document.body.style.overflow = ""
        // Reset file input
        this.fileInputTarget.value = ""
        // Reset preview về trạng thái ban đầu
        this.resetPreview()
    }

    // Đóng popup khi click ra ngoài (vào overlay)
    closeOnOverlay(event) {
        if (event.target === this.popupTarget) {
            this.close()
        }
    }

    // Ngăn sự kiện click lan ra overlay
    stopPropagation(event) {
        event.stopPropagation()
    }

    // Mở dialog chọn file
    openFilePicker() {
        this.fileInputTarget.click()
    }

    // Preview ảnh khi chọn file
    preview(event) {
        const input = event.target
        if (input.files && input.files[0]) {
            const reader = new FileReader()
            reader.onload = (e) => {
                this.previewImgTarget.src = e.target.result
                this.previewImgTarget.classList.remove("tw-hidden")
                
                if (this.hasPlaceholderTarget) {
                    this.placeholderTarget.classList.add("tw-hidden")
                }
                
                // Enable nút xác nhận
                this.confirmBtnTarget.disabled = false
            }
            reader.readAsDataURL(input.files[0])
        }
    }

    // Reset preview về trạng thái ban đầu
    resetPreview() {
        if (this.currentAvatarValue) {
            this.previewImgTarget.src = this.currentAvatarValue
            this.previewImgTarget.classList.remove("tw-hidden")
        } else {
            this.previewImgTarget.classList.add("tw-hidden")
            if (this.hasPlaceholderTarget) {
                this.placeholderTarget.classList.remove("tw-hidden")
            }
        }
        // Disable nút xác nhận
        this.confirmBtnTarget.disabled = true
    }

    // Xóa avatar
    delete() {
        if (confirm("Bạn có chắc chắn muốn xóa ảnh đại diện?")) {
            // Tạo form xóa avatar
            const form = document.createElement("form")
            form.method = "POST"
            form.action = this.deleteUrlValue
            
            const csrfInput = document.createElement("input")
            csrfInput.type = "hidden"
            csrfInput.name = "_token"
            csrfInput.value = this.csrfTokenValue
            
            const methodInput = document.createElement("input")
            methodInput.type = "hidden"
            methodInput.name = "_method"
            methodInput.value = "DELETE"
            
            form.appendChild(csrfInput)
            form.appendChild(methodInput)
            document.body.appendChild(form)
            form.submit()
        }
    }
}
