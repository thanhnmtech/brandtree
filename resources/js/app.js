import "./bootstrap";

// Alpine.js is bundled with Livewire v3+, no need to import separately
// Livewire automatically manages Alpine initialization

// Import Stimulus
import { Application } from "@hotwired/stimulus";

// Import Stimulus controllers
import BrandFormController from "./controllers/brand_form_controller";
import BrandFilterController from "./controllers/brand_filter_controller";
import MemberManagementController from "./controllers/member_management_controller";
import DropdownController from "./controllers/dropdown_controller";
import PaymentStatusController from "./controllers/payment_status_controller";
import StepCardController from "./controllers/step_card_controller";

// Start Stimulus application
const application = Application.start();

// Register controllers
application.register("brand-form", BrandFormController);
application.register("brand-filter", BrandFilterController);
application.register("member-management", MemberManagementController);
application.register("dropdown", DropdownController);
application.register("payment-status", PaymentStatusController);
application.register("step-card", StepCardController);

// Make Stimulus available globally (optional, for debugging)
window.Stimulus = application;

// ===========================================
// Form Submit Loading Button
// ===========================================
document.addEventListener("DOMContentLoaded", function () {
    const forms = document.querySelectorAll("form");

    forms.forEach((form) => {
        form.addEventListener("submit", function (e) {
            const submitBtn = form.querySelector('button[type="submit"]');

            if (submitBtn && !submitBtn.disabled) {
                submitBtn.dataset.originalHtml = submitBtn.innerHTML;
                submitBtn.disabled = true;

                submitBtn.innerHTML = `
                    <span class="tw-flex tw-items-center tw-justify-center tw-gap-2">
                        <svg class="tw-animate-spin tw-h-5 tw-w-5 tw-text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="tw-opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="tw-opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Đang xử lý...</span>
                    </span>
                `;

                submitBtn.style.opacity = "0.7";
                submitBtn.style.cursor = "not-allowed";

                setTimeout(() => {
                    if (!form.checkValidity()) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = submitBtn.dataset.originalHtml;
                        submitBtn.style.opacity = "1";
                        submitBtn.style.cursor = "pointer";
                    }
                }, 100);
            }
        });
    });
});

// Reset loading state when navigating back via browser back/forward button (bfcache)
window.addEventListener("pageshow", function (event) {
    if (event.persisted) {
        document.querySelectorAll('button[type="submit"]').forEach((btn) => {
            if (btn.disabled && btn.dataset.originalHtml) {
                btn.disabled = false;
                btn.innerHTML = btn.dataset.originalHtml;
                btn.style.opacity = "1";
                btn.style.cursor = "pointer";
            }
        });
    }
});

// ===========================================
// Toastify Configuration
// ===========================================
window.showToast = function (message, type = "success") {
    const config = {
        success: {
            background: "linear-gradient(to right, #00b09b, #96c93d)",
            icon: "✓",
        },
        error: {
            background: "linear-gradient(to right, #ff5f6d, #ffc371)",
            icon: "✕",
        },
        warning: {
            background: "linear-gradient(to right, #f093fb, #f5576c)",
            icon: "⚠",
        },
        info: {
            background: "linear-gradient(to right, #4facfe, #00f2fe)",
            icon: "ℹ",
        },
    };

    const settings = config[type] || config.info;

    Toastify({
        text: settings.icon + "  " + message,
        duration: 4000,
        gravity: "top",
        position: "right",
        stopOnFocus: true,
        style: { background: settings.background },
    }).showToast();
};

window.showNotification = window.showToast;
