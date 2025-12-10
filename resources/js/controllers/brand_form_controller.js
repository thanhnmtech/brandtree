import { Controller } from "@hotwired/stimulus"

export default class extends Controller {
    static targets = [
        "addModal",
        "editModal",
        "deleteModal",
        "brandForm",
        "logoInput",
        "logoPreview",
        "brandNameInput",
        "deleteConfirmInput",
        "submitButton"
    ]

    static values = {
        brandName: String,
        brandLogo: String,
        deleteUrl: String,
        redirectUrl: String,
        hasErrors: Boolean,
        modalMode: String
    }

    connect() {
        this.isSubmitting = false
        this.formErrors = {}
        this.deleteConfirmName = ''

        // Auto-open modal if there are validation errors
        if (this.hasErrorsValue) {
            if (this.modalModeValue === 'create') {
                this.openAdd()
            } else if (this.modalModeValue === 'edit') {
                this.openEdit()
            }
        }

        // Set initial logo preview if exists
        if (this.brandLogoValue && this.hasLogoPreviewTarget) {
            this.showLogoPreview(this.brandLogoValue)
        }
    }

    // ==================== MODAL MANAGEMENT ====================

    openAdd() {
        this.addModalTarget.classList.remove('tw-hidden')
        this.addModalTarget.style.display = 'flex'
        this.addModalTarget.style.alignItems = 'center'
        this.addModalTarget.style.justifyContent = 'center'
        this.formErrors = {}
        setTimeout(() => {
            this.brandNameInputTarget?.focus()
        }, 100)
    }

    openEdit() {
        this.editModalTarget.classList.remove('tw-hidden')
        this.editModalTarget.style.display = 'flex'
        this.editModalTarget.style.alignItems = 'center'
        this.editModalTarget.style.justifyContent = 'center'
        this.formErrors = {}
        setTimeout(() => {
            this.brandNameInputTarget?.focus()
        }, 100)
    }

    openDelete() {
        this.deleteModalTarget.classList.remove('tw-hidden')
        this.deleteModalTarget.style.display = 'flex'
        this.deleteModalTarget.style.alignItems = 'center'
        this.deleteModalTarget.style.justifyContent = 'center'
        this.deleteConfirmName = ''
        setTimeout(() => {
            this.deleteConfirmInputTarget?.focus()
        }, 100)
    }

    closeAdd() {
        this.addModalTarget.classList.add('tw-hidden')
        this.addModalTarget.style.display = 'none'
        this.resetForm()
    }

    closeEdit() {
        this.editModalTarget.classList.add('tw-hidden')
        this.editModalTarget.style.display = 'none'
        this.resetLogoPreview()
        this.formErrors = {}
    }

    closeDelete() {
        this.deleteModalTarget.classList.add('tw-hidden')
        this.deleteModalTarget.style.display = 'none'
        this.deleteConfirmName = ''
    }

    closeOnBackdrop(event) {
        // Close modal when clicking on backdrop (not the modal content)
        if (event.target === event.currentTarget) {
            if (this.hasAddModalTarget && !this.addModalTarget.classList.contains('tw-hidden')) {
                this.closeAdd()
            } else if (this.hasEditModalTarget && !this.editModalTarget.classList.contains('tw-hidden')) {
                this.closeEdit()
            } else if (this.hasDeleteModalTarget && !this.deleteModalTarget.classList.contains('tw-hidden')) {
                this.closeDelete()
            }
        }
    }

    stopPropagation(event) {
        // Stop event from bubbling up to backdrop
        event.stopPropagation()
    }

    // ==================== FORM MANAGEMENT ====================

    resetForm() {
        if (this.hasBrandFormTarget) {
            this.brandFormTarget.reset()
        }
        this.clearLogoPreview()
        this.formErrors = {}
    }

    // ==================== LOGO PREVIEW ====================

    previewLogo(event) {
        const file = event.target.files[0]
        if (!file) return

        // Validate file size (2MB)
        if (file.size > 2 * 1024 * 1024) {
            if (window.showToast) {
                window.showToast('Logo không được lớn hơn 2MB', 'error')
            }
            event.target.value = ''
            return
        }

        // Validate file type
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/svg+xml']
        if (!validTypes.includes(file.type)) {
            if (window.showToast) {
                window.showToast('Chỉ chấp nhận file ảnh (JPEG, PNG, GIF, SVG)', 'error')
            }
            event.target.value = ''
            return
        }

        const reader = new FileReader()
        reader.onload = (e) => {
            this.showLogoPreview(e.target.result)
        }
        reader.readAsDataURL(file)
    }

    showLogoPreview(dataUrl) {
        if (this.hasLogoPreviewTarget) {
            this.logoPreviewTarget.src = dataUrl
            this.logoPreviewTarget.closest('[data-logo-container]').classList.remove('tw-hidden')
            this.logoPreviewTarget.closest('[data-logo-container]').parentElement.querySelector('[data-logo-placeholder]')?.classList.add('tw-hidden')
        }
    }

    clearLogoPreview(event) {
        // Stop event from bubbling to parent (prevent triggering file input)
        if (event) {
            event.stopPropagation()
        }

        if (this.hasLogoPreviewTarget) {
            this.logoPreviewTarget.src = ''
            this.logoPreviewTarget.closest('[data-logo-container]').classList.add('tw-hidden')
            this.logoPreviewTarget.closest('[data-logo-container]').parentElement.querySelector('[data-logo-placeholder]')?.classList.remove('tw-hidden')
        }
        if (this.hasLogoInputTarget) {
            this.logoInputTarget.value = ''
        }
    }

    resetLogoPreview() {
        // For edit mode: reset to original brand logo
        if (this.brandLogoValue) {
            this.showLogoPreview(this.brandLogoValue)
        } else {
            this.clearLogoPreview()
        }
    }

    // ==================== FORM SUBMISSION ====================

    async submitBrandForm(event) {
        event.preventDefault()

        this.isSubmitting = true
        this.formErrors = {}
        this.updateSubmitButton(true)

        const form = event.target
        const formData = new FormData(form)

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })

            const data = await response.json()

            if (response.ok) {
                // Close modal
                if (this.hasAddModalTarget && !this.addModalTarget.classList.contains('tw-hidden')) {
                    this.closeAdd()
                } else if (this.hasEditModalTarget && !this.editModalTarget.classList.contains('tw-hidden')) {
                    this.closeEdit()
                }

                // Show notification
                if (window.showNotification) {
                    window.showNotification(data.message, 'success')
                }

                // Redirect
                if (data.redirect) {
                    window.location.href = data.redirect
                } else {
                    window.location.reload()
                }
            } else {
                // Validation errors
                console.error('Validation failed:', response.status, data)
                if (data.errors) {
                    this.formErrors = data.errors
                    console.log('Form errors:', this.formErrors)

                    // Show toast for each error
                    Object.keys(data.errors).forEach(field => {
                        const errorMessages = data.errors[field]
                        errorMessages.forEach(message => {
                            if (window.showToast) {
                                window.showToast(message, 'error')
                            }
                        })
                    })
                } else {
                    const errorMessage = data.message || 'Có lỗi xảy ra. Vui lòng thử lại.'
                    if (window.showToast) {
                        window.showToast(errorMessage, 'error')
                    }
                }
            }
        } catch (error) {
            console.error('Error:', error)
            if (window.showToast) {
                window.showToast('Có lỗi xảy ra. Vui lòng thử lại.', 'error')
            }
        } finally {
            this.isSubmitting = false
            this.updateSubmitButton(false)
        }
    }

    updateSubmitButton(isSubmitting) {
        if (this.hasSubmitButtonTarget) {
            this.submitButtonTarget.disabled = isSubmitting

            const spinner = this.submitButtonTarget.querySelector('[data-spinner]')
            const icon = this.submitButtonTarget.querySelector('[data-icon]')
            const text = this.submitButtonTarget.querySelector('[data-text]')

            if (spinner) {
                spinner.classList.toggle('tw-hidden', !isSubmitting)
            }
            if (icon) {
                icon.classList.toggle('tw-hidden', isSubmitting)
            }
            if (text) {
                text.textContent = isSubmitting ? 'Đang xử lý...' : text.dataset.originalText
            }
        }
    }

    // ==================== DELETE BRAND ====================

    updateDeleteConfirmName(event) {
        this.deleteConfirmName = event.target.value
    }

    async submitDeleteBrand(event) {
        event.preventDefault()

        if (this.deleteConfirmName !== this.brandNameValue) {
            if (window.showToast) {
                window.showToast('Tên thương hiệu không chính xác!', 'error')
            }
            return
        }

        this.isSubmitting = true

        try {
            const response = await fetch(this.deleteUrlValue, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })

            const data = await response.json()

            if (response.ok) {
                this.closeDelete()

                if (window.showNotification) {
                    window.showNotification(data.message, 'success')
                }

                setTimeout(() => {
                    window.location.href = data.redirect || this.redirectUrlValue
                }, 500)
            } else {
                if (window.showToast) {
                    window.showToast(data.message || 'Có lỗi xảy ra. Vui lòng thử lại.', 'error')
                }
            }
        } catch (error) {
            console.error('Error:', error)
            if (window.showToast) {
                window.showToast('Có lỗi xảy ra. Vui lòng thử lại.', 'error')
            }
        } finally {
            this.isSubmitting = false
        }
    }
}
