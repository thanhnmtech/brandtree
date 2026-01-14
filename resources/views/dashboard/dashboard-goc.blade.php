<x-app-layout>
<main class="tw-mt-[36px] tw-flex tw-flex-col tw-gap-10">

    @include('dashboard.partials.hero')

    @include('dashboard.partials.header')

    <div class="tw-px-8 tw-w-full tw-flex tw-gap-10">
        <div class="tw-flex tw-flex-col tw-flex-1 tw-gap-6">
            @include('dashboard.partials.steps')
        </div>

        <aside class="tw-hidden md:tw-flex tw-flex-col tw-w-[350px] tw-gap-6">
            @include('dashboard.partials.sidebar-progress')
            @include('dashboard.partials.sidebar-next-step')
        </aside>
    </div>

</main>
</x-app-layout>

<script>
            class StepCard extends HTMLElement {
                async connectedCallback() {
                    const template = await fetch(
                        "./widget/step_card.html"
                    ).then((r) => r.text());
                    this.innerHTML = template;

                    const root = this.querySelector(".step-card");

                    // ===== ATTRIBUTES =====
                    const state = this.getAttribute("state"); // locked | ready
                    const step = this.getAttribute("step");
                    const title = this.getAttribute("title");
                    const description = this.getAttribute("description");
                    const aiFeature = this.getAttribute("aifeature");

                    // ===== CONTENT =====
                    root.querySelector(
                        ".step-number"
                    ).textContent = `BƯỚC ${step}`;
                    root.querySelector(".title").textContent = title;
                    root.querySelector(".description").textContent =
                        description;
                    root.querySelector(".ai-feature").textContent = aiFeature;

                    // ===== STATE CONFIG =====
                    const presets = {
                        locked: {
                            root: [
                                "tw-bg-beige",
                                "tw-border",
                                "tw-border-gray-200",
                            ],
                            icon: ["ri-lock-line", "tw-text-[#7B7773]"],
                            stepColor: "tw-text-[#7B7773]",
                            badgeText: "Đã Khóa",
                            badgeClass: ["tw-bg-[#e7e5df]", "tw-text-gray-600"],
                            textColor: "tw-text-[#7B7773]",
                            titleColor: "tw-text-[#7B7773]",
                            aiBox: [
                                "tw-bg-[#F7F6F4]",
                                "tw-border-[#E0EAE6]",
                                "tw-text-[#7B7773]",
                            ],
                            show: { start: false, result: false, locked: true },
                        },

                        ready: {
                            root: [
                                "tw-bg-gradient-to-r",
                                "tw-from-vlbcGreen1",
                                "tw-to-vlbcGreen2",
                            ],
                            icon: ["ri-time-line", "tw-text-vlbcGreen"],
                            stepColor: "tw-text-[#F7C163]",
                            badgeText: "Sẵn Sàng",
                            badgeClass: ["tw-bg-vlbcgreen", "tw-text-white"],
                            textColor: "tw-text-white/90",
                            titleColor: "tw-text-[#F2F307]",
                            aiBox: [
                                "tw-bg-white/20",
                                "tw-border-white/30",
                                "tw-text-white/90",
                            ],
                            show: { start: true, result: true, locked: false },
                        },
                    };

                    const p = presets[state];

                    // ===== APPLY STATE =====
                    root.classList.add(...p.root);

                    root.querySelector(".icon").classList.add(...p.icon);
                    root.querySelector(".step-number").classList.add(
                        p.stepColor
                    );
                    root.querySelector(".title").classList.add(p.titleColor);
                    root.querySelector(".description").classList.add(
                        p.textColor
                    );

                    const badge = root.querySelector(".badge");
                    badge.textContent = p.badgeText;
                    badge.classList.add(...p.badgeClass);

                    const aiBox = root.querySelector(".ai-box");
                    aiBox.classList.add(...p.aiBox);

                    // buttons
                    root.querySelector(".start-btn").classList.toggle(
                        "tw-hidden",
                        !p.show.start
                    );
                    root.querySelector(".result-btn").classList.toggle(
                        "tw-hidden",
                        !p.show.result
                    );
                    root.querySelector(".locked-btn").classList.toggle(
                        "tw-hidden",
                        !p.show.locked
                    );
                }
            }

            customElements.define("step-card", StepCard);

            document.addEventListener("click", function (e) {
                const body = document.body;

                /* ================= BRAND SWITCHER ================= */
                const brandBtn = document.getElementById("brandSwitcherBtn");
                const brandPanel =
                    document.getElementById("brandSwitcherPanel");
                const brandClose =
                    document.getElementById("brandSwitcherClose");

                if (brandBtn && brandPanel) {
                    const isMobile = window.innerWidth < 768; // md breakpoint

                    // Toggle panel
                    if (brandBtn === e.target || brandBtn.contains(e.target)) {
                        e.stopPropagation();

                        const isOpening =
                            brandPanel.classList.contains("tw-hidden");

                        if (isOpening) {
                            // ✅ luôn hiện giữa màn hình
                            brandPanel.classList.remove("tw-hidden");

                            brandPanel.style.top = "50%";
                            brandPanel.style.left = "50%";
                            brandPanel.style.transform =
                                "translate(-50%, -50%)";

                            body.classList.add("tw-overflow-hidden");
                        } else {
                            brandPanel.classList.add("tw-hidden");
                            body.classList.remove("tw-overflow-hidden");
                        }

                        return;
                    }

                    // Close button
                    if (brandClose && brandClose.contains(e.target)) {
                        brandPanel.classList.add("tw-hidden");
                        body.classList.remove("tw-overflow-hidden");
                        return;
                    }

                    // Click outside
                    if (!brandPanel.contains(e.target)) {
                        brandPanel.classList.add("tw-hidden");
                        body.classList.remove("tw-overflow-hidden");
                    }
                }

                /* ================= EDIT BRAND OVERLAY ================= */
                const editBtn = document.querySelector(".ri-settings-3-line");
                const editOverlay = document.getElementById("editBrandOverlay");
                const editClose = document.getElementById("editBrandClose");
                const editContent = editOverlay?.querySelector(":scope > div");

                if (editBtn && editOverlay && editContent) {
                    // Open overlay
                    if (editBtn === e.target || editBtn.contains(e.target)) {
                        e.stopPropagation();
                        editOverlay.classList.remove("tw-hidden");
                        body.classList.add("tw-overflow-hidden");
                        return;
                    }

                    // Close button
                    if (editClose && editClose.contains(e.target)) {
                        editOverlay.classList.add("tw-hidden");
                        body.classList.remove("tw-overflow-hidden");
                        return;
                    }

                    // Click outside modal
                    if (!editContent.contains(e.target)) {
                        editOverlay.classList.add("tw-hidden");
                        body.classList.remove("tw-overflow-hidden");
                    }
                }
            });
</script>