import { Controller } from "@hotwired/stimulus"

export default class extends Controller {
    static targets = [
        "inviteModal",
        "editModal",
        "deleteModal",
        "roleInput",
        "editForm",
        "deleteForm",
        "editMemberIdInput",
        "editRoleAdmin",
        "editRoleEditor",
        "deleteMemberName",
        "rootPerm",
        "stemPerm",
        "crownPerm"
    ]

    static values = {
        currentRole: { type: String, default: 'editor' }
    }

    connect() {
        this.formErrors = {}
        // Set initial permissions display for editor role (default)
        this.updatePermissionDisplay('editor')
    }

    // ==================== MODAL MANAGEMENT ====================

    openInvite() {
        this.inviteModalTarget.classList.remove('tw-hidden')
        this.inviteModalTarget.style.display = 'flex'
        this.inviteModalTarget.style.alignItems = 'center'
        this.inviteModalTarget.style.justifyContent = 'center'
        this.formErrors = {}
    }

    openEdit(event) {
        const button = event.currentTarget
        const memberId = button.dataset.memberId
        const memberRole = button.dataset.memberRole

        // Update form action
        const baseUrl = this.editFormTarget.action
        this.editFormTarget.action = `${baseUrl}/${memberId}`

        // Set member ID
        this.editMemberIdInputTarget.value = memberId

        // Check the appropriate radio button
        if (memberRole === 'admin') {
            this.editRoleAdminTarget.checked = true
        } else {
            this.editRoleEditorTarget.checked = true
        }

        // Show modal
        this.editModalTarget.classList.remove('tw-hidden')
        this.editModalTarget.style.display = 'flex'
        this.editModalTarget.style.alignItems = 'center'
        this.editModalTarget.style.justifyContent = 'center'
        this.formErrors = {}
    }

    openDelete(event) {
        const button = event.currentTarget
        const memberId = button.dataset.memberId
        const memberName = button.dataset.memberName

        // Update form action
        const baseUrl = this.deleteFormTarget.action
        this.deleteFormTarget.action = `${baseUrl}/${memberId}`

        // Set member name in the confirmation message
        this.deleteMemberNameTarget.textContent = memberName

        // Show modal
        this.deleteModalTarget.classList.remove('tw-hidden')
        this.deleteModalTarget.style.display = 'flex'
        this.deleteModalTarget.style.alignItems = 'center'
        this.deleteModalTarget.style.justifyContent = 'center'
    }

    closeInvite() {
        this.inviteModalTarget.classList.add('tw-hidden')
        this.inviteModalTarget.style.display = 'none'
        this.formErrors = {}
    }

    closeEdit() {
        this.editModalTarget.classList.add('tw-hidden')
        this.editModalTarget.style.display = 'none'
        this.formErrors = {}
    }

    closeDelete() {
        this.deleteModalTarget.classList.add('tw-hidden')
        this.deleteModalTarget.style.display = 'none'
    }

    stopPropagation(event) {
        // Prevent clicks inside modal from closing it
        event.stopPropagation()
    }

    // ==================== PERMISSION DISPLAY ====================

    updatePermissions(event) {
        const role = event.target.value
        this.updatePermissionDisplay(role)
    }

    updatePermissionDisplay(role) {
        if (!this.hasRootPermTarget || !this.hasStemPermTarget || !this.hasCrownPermTarget) {
            return
        }

        // Permission matrix based on role
        const permissions = {
            admin: {
                root: { text: 'Toàn quyền', class: 'tw-bg-green-100 tw-text-green-700' },
                stem: { text: 'Toàn quyền', class: 'tw-bg-green-100 tw-text-green-700' },
                crown: { text: 'Toàn quyền', class: 'tw-bg-green-100 tw-text-green-700' }
            },
            editor: {
                root: { text: 'Chỉ xem', class: 'tw-bg-gray-100 tw-text-gray-600' },
                stem: { text: 'Chỉ xem', class: 'tw-bg-gray-100 tw-text-gray-600' },
                crown: { text: 'Toàn quyền', class: 'tw-bg-green-100 tw-text-green-700' }
            }
        }

        const rolePerms = permissions[role] || permissions.editor

        // Update Root permission
        this.rootPermTarget.textContent = rolePerms.root.text
        this.rootPermTarget.className = `tw-text-xs tw-font-medium tw-px-2 tw-py-1 tw-rounded-full ${rolePerms.root.class}`

        // Update Stem permission
        this.stemPermTarget.textContent = rolePerms.stem.text
        this.stemPermTarget.className = `tw-text-xs tw-font-medium tw-px-2 tw-py-1 tw-rounded-full ${rolePerms.stem.class}`

        // Update Crown permission
        this.crownPermTarget.textContent = rolePerms.crown.text
        this.crownPermTarget.className = `tw-text-xs tw-font-medium tw-px-2 tw-py-1 tw-rounded-full ${rolePerms.crown.class}`
    }
}
