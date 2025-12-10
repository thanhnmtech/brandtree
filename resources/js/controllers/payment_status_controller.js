import { Controller } from "@hotwired/stimulus"

export default class extends Controller {
    static values = {
        status: String,
        checkUrl: String,
        maxChecks: { type: Number, default: 60 },
        intervalMs: { type: Number, default: 5000 }
    }

    connect() {
        this.isChecking = false
        this.pollingInterval = null
        this.checkCount = 0
        this.timeElapsed = 0

        // Start polling if status is pending
        if (this.statusValue === 'pending') {
            this.startPolling()
        }

        // Handle visibility change
        this.handleVisibilityChange = this.handleVisibilityChange.bind(this)
        document.addEventListener('visibilitychange', this.handleVisibilityChange)

        // Stop polling on page unload
        this.handleBeforeUnload = this.handleBeforeUnload.bind(this)
        window.addEventListener('beforeunload', this.handleBeforeUnload)
    }

    disconnect() {
        this.stopPolling()
        document.removeEventListener('visibilitychange', this.handleVisibilityChange)
        window.removeEventListener('beforeunload', this.handleBeforeUnload)
    }

    handleVisibilityChange() {
        if (document.hidden) {
            this.stopPolling()
        } else if (this.statusValue === 'pending') {
            this.checkCount = 0
            this.timeElapsed = 0
            this.startPolling()
        }
    }

    handleBeforeUnload() {
        this.stopPolling()
    }

    async checkPaymentStatus() {
        if (this.statusValue !== 'pending') return

        this.isChecking = true
        this.updateCheckingState(true)

        try {
            const response = await fetch(this.checkUrlValue, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })

            const data = await response.json()

            if (data.completed) {
                this.statusValue = data.status
                this.stopPolling()

                if (window.showNotification) {
                    window.showNotification(data.message, 'success')
                }

                // Update UI to show completed state
                this.showCompletedView()

                // Redirect after delay
                setTimeout(() => {
                    window.location.href = data.redirect
                }, 2000)
            }
        } catch (error) {
            console.error('Error checking payment status:', error)
        } finally {
            setTimeout(() => {
                this.isChecking = false
                this.updateCheckingState(false)
            }, 500)
        }
    }

    startPolling() {
        // Prevent multiple intervals
        if (this.pollingInterval) {
            return
        }

        // Check immediately
        this.checkPaymentStatus()

        // Then check every interval
        this.pollingInterval = setInterval(() => {
            this.checkCount++
            this.timeElapsed = this.checkCount * (this.intervalMsValue / 1000)

            // Update time display
            this.updateTimeElapsed()

            if (this.checkCount >= this.maxChecksValue) {
                this.stopPolling()
                return
            }

            this.checkPaymentStatus()
        }, this.intervalMsValue)
    }

    stopPolling() {
        if (this.pollingInterval) {
            clearInterval(this.pollingInterval)
            this.pollingInterval = null
        }
    }

    updateCheckingState(isChecking) {
        // Optional: Add visual feedback when checking
        const statusBar = this.element.querySelector('[data-payment-status-bar]')
        if (statusBar) {
            if (isChecking) {
                statusBar.classList.add('tw-opacity-75')
            } else {
                statusBar.classList.remove('tw-opacity-75')
            }
        }
    }

    updateTimeElapsed() {
        const timeDisplay = this.element.querySelector('[data-payment-time-elapsed]')
        if (timeDisplay && this.timeElapsed > 0) {
            const timeContainer = this.element.querySelector('[data-payment-time-container]')
            if (timeContainer) {
                timeContainer.classList.remove('tw-hidden')
            }
            timeDisplay.textContent = this.formatTime(this.timeElapsed)
        }
    }

    formatTime(seconds) {
        const mins = Math.floor(seconds / 60)
        const secs = seconds % 60
        return mins > 0 ? `${mins}m ${secs}s` : `${secs}s`
    }

    showCompletedView() {
        // Hide pending view
        const pendingView = this.element.querySelector('[data-payment-pending]')
        if (pendingView) {
            pendingView.classList.add('tw-hidden')
        }

        // Show completed view
        const completedView = this.element.querySelector('[data-payment-completed]')
        if (completedView) {
            completedView.classList.remove('tw-hidden')
            completedView.classList.add('slide-up')
        }
    }
}
