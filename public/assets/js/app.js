document.addEventListener("click", function (e) {

    /* ========= OPEN MODAL ========= */
    const openBtn = e.target.closest("[data-open-modal]");
    if (openBtn) {
        const modalId = openBtn.dataset.openModal;
        const modal = document.getElementById(modalId);
        if (modal) modal.classList.remove("tw-hidden");
        return;
    }

    /* ========= CLOSE MODAL ========= */
    const closeBtn = e.target.closest("[data-close-modal]");
    if (closeBtn) {
        const modal = closeBtn.closest(".tw-fixed");
        if (modal) modal.classList.add("tw-hidden");
        return;
    }

    /* ========= CLICK OVERLAY ========= */
    if (
        e.target.classList.contains("tw-fixed") &&
        e.target.id
    ) {
        e.target.classList.add("tw-hidden");
    }
});