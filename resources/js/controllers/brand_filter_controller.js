import { Controller } from "@hotwired/stimulus"

export default class extends Controller {
    static targets = ["search", "status", "orderBy", "cardsContainer", "loading"]

    static values = {
        url: String,
        debounce: { type: Number, default: 300 }
    }

    connect() {
        this.searchTimeout = null
    }

    disconnect() {
        if (this.searchTimeout) {
            clearTimeout(this.searchTimeout)
        }
    }

    search() {
        // Debounce search input
        if (this.searchTimeout) {
            clearTimeout(this.searchTimeout)
        }

        this.searchTimeout = setTimeout(() => {
            this.filter()
        }, this.debounceValue)
    }

    filter() {
        const params = new URLSearchParams()

        // Search
        if (this.hasSearchTarget && this.searchTarget.value) {
            params.set('search', this.searchTarget.value)
        }

        // Status
        if (this.hasStatusTarget && this.statusTarget.value) {
            params.set('status', this.statusTarget.value)
        }

        // Order by
        if (this.hasOrderByTarget && this.orderByTarget.value) {
            params.set('order_by', this.orderByTarget.value)
        }

        this.fetchBrands(params)
    }

    async fetchBrands(params) {
        // Show loading
        this.showLoading()

        try {
            const url = `${this.urlValue}?${params.toString()}`

            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })

            if (!response.ok) {
                throw new Error('Network response was not ok')
            }

            const data = await response.json()

            // Update cards
            if (this.hasCardsContainerTarget) {
                this.cardsContainerTarget.innerHTML = data.html
            }

            // Update stats
            this.updateStats(data.stats)

            // Update URL without reload
            const newUrl = `${window.location.pathname}?${params.toString()}`
            window.history.pushState({}, '', newUrl)

        } catch (error) {
            console.error('Error fetching brands:', error)
            if (window.showToast) {
                window.showToast('Có lỗi xảy ra khi tải dữ liệu', 'error')
            }
        } finally {
            this.hideLoading()
        }
    }

    updateStats(stats) {
        // Update stat cards
        const seedlingEl = document.querySelector('[data-stat="seedling"]')
        const growingEl = document.querySelector('[data-stat="growing"]')
        const completedEl = document.querySelector('[data-stat="completed"]')
        const totalEl = document.querySelector('[data-stat="total"]')

        if (seedlingEl) seedlingEl.textContent = stats.seedlingCount
        if (growingEl) growingEl.textContent = stats.growingCount
        if (completedEl) completedEl.textContent = stats.completedCount
        if (totalEl) totalEl.textContent = `${stats.activeBrands}/${stats.totalBrands}`
    }

    showLoading() {
        if (this.hasLoadingTarget) {
            this.loadingTarget.style.display = 'flex'
        }
        if (this.hasCardsContainerTarget) {
            this.cardsContainerTarget.classList.add('tw-opacity-50', 'tw-pointer-events-none')
        }
    }

    hideLoading() {
        if (this.hasLoadingTarget) {
            this.loadingTarget.style.display = 'none'
        }
        if (this.hasCardsContainerTarget) {
            this.cardsContainerTarget.classList.remove('tw-opacity-50', 'tw-pointer-events-none')
        }
    }

    // Reset filters
    reset() {
        if (this.hasSearchTarget) this.searchTarget.value = ''
        if (this.hasStatusTarget) this.statusTarget.value = ''
        if (this.hasOrderByTarget) this.orderByTarget.value = 'updated_at'

        this.filter()
    }
}
