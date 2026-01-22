import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    // Khai báo các phần tử cần thao tác trong DOM
    static targets = [
        "card", // Bao ngoài cùng để đổi màu nền
        "icon", // Thẻ <i> icon
        "stepText", // Chữ "BƯỚC G1"
        "badge", // Cái nhãn "Sẵn sàng" / "Đã khóa"
        "title", // Tiêu đề chính
        "description", // Mô tả
        "aiFeature", // Box AI
        "actions", // Khu vực chứa nút bấm
        "startButton", // Nút Bắt đầu (link)
        "resultButton", // Nút Kết quả (link)
        "lockedButton", // Nút Đã khóa (button disabled)
    ];

    static values = {
        state: String, // 'locked' | 'ready' | 'completed'
    };

    connect() {
        this.render();
    }

    stateValueChanged() {
        this.render();
    }

    render() {
        const state = this.stateValue || "locked";
        const config = this.getStyleConfig(state);

        // 1. Reset & Apply Root Classes (Màu nền card)
        this.cardTarget.className = `tw-rounded-xl tw-p-8 tw-space-y-4 tw-shadow-[0_4px_4px_0_rgba(0,0,0,0.05)] ${config.card}`;

        // 2. Icon Styling
        this.iconTarget.className = `${config.icon} tw-text-2xl`;

        // 3. Text Styling
        this.stepTextTarget.className = `tw-font-semibold ${config.stepText}`;
        this.badgeTarget.className = `tw-px-3 tw-py-1 tw-text-xs tw-rounded-full tw-font-medium ${config.badge}`;
        // this.badgeTarget.textContent = config.badgeLabel;

        this.titleTarget.className = `tw-text-xl tw-font-semibold tw-mt-1 ${config.title}`;
        this.descriptionTarget.className = `tw-leading-relaxed ${config.description}`;

        // 4. AI Feature Box
        this.aiFeatureTarget.className = `tw-rounded-lg tw-p-4 tw-text-sm tw-border ${config.aiFeature}`;

        // 5. Buttons Visibility
        this.startButtonTarget.classList.add("tw-hidden");
        this.resultButtonTarget.classList.add("tw-hidden");
        this.lockedButtonTarget.classList.add("tw-hidden");

        if (state === "locked") {
            this.lockedButtonTarget.classList.remove("tw-hidden");
        } else {
            this.resultButtonTarget.classList.remove("tw-hidden");
            this.startButtonTarget.classList.remove("tw-hidden");
        }
    }

    // Cấu hình giao diện cho từng trạng thái (tương tự Web Component cũ của bạn)
    getStyleConfig(state) {
        const presets = {
            locked: {
                card: "tw-bg-beige tw-border tw-border-gray-200",
                icon: "ri-lock-line tw-text-[#7B7773]",
                stepText: "tw-text-[#7B7773]",
                badge: "tw-bg-[#e7e5df] tw-text-gray-600",
                badgeLabel: "Đã khóa",
                title: "tw-text-[#7B7773]",
                description: "tw-text-[#7B7773]",
                aiFeature: "tw-bg-[#F7F6F4] tw-border-[#E0EAE6] tw-text-[#7B7773]",
            },
            ready: {
                // Áp dụng cho cả 'ready' và 'active'
                card: "tw-bg-gradient-to-r tw-from-vlbcGreen1 tw-to-vlbcGreen2",
                icon: "ri-time-line tw-text-vlbcGreen",
                stepText: "tw-text-[#F7C163]",
                badge: "tw-bg-vlbcgreen tw-text-white",
                badgeLabel: "Sẵn sàng",
                title: "tw-text-[#F2F307]",
                description: "tw-text-white/90",
                aiFeature: "tw-bg-white/20 tw-border-white/30 tw-text-white/90",
            },
            completed: {
                card: "tw-bg-gradient-to-r tw-from-vlbcGreen1 tw-to-vlbcGreen2",
                icon: "ri-check-line tw-text-white",
                stepText: "tw-text-[#F7C163]",
                badge: "tw-bg-vlbcgreen tw-text-white",
                badgeLabel: "Đã hoàn thành",
                title: "tw-text-[#F2F307]",
                description: "tw-text-white/90",
                aiFeature: "tw-bg-white/20 tw-border-white/30 tw-text-white/90",
            },
        };

        // Nếu state là 'active' thì dùng style của 'ready'
        if (state === "ready") return presets.ready;

        return presets[state] || presets.locked;
    }
}
