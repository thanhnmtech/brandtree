import './bootstrap';

// Import Stimulus
import { Application } from "@hotwired/stimulus"

// Import Stimulus controllers
import BrandFormController from "./controllers/brand_form_controller"
import MemberManagementController from "./controllers/member_management_controller"
import DropdownController from "./controllers/dropdown_controller"
import PaymentStatusController from "./controllers/payment_status_controller"

// Start Stimulus application
const application = Application.start()

// Register controllers
application.register("brand-form", BrandFormController)
application.register("member-management", MemberManagementController)
application.register("dropdown", DropdownController)
application.register("payment-status", PaymentStatusController)

// Make Stimulus available globally (optional, for debugging)
window.Stimulus = application.start();
