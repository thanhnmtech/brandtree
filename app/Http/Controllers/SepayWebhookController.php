<?php

namespace App\Http\Controllers;

use App\Models\BrandSubscription;
use App\Models\Payment;
use App\Services\SepayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SepayWebhookController extends Controller
{
    public function __construct(
        protected SepayService $sepayService
    ) {}

    /**
     * Handle Sepay webhook callback.
     */
    public function handle(Request $request): JsonResponse
    {
        // Log incoming webhook for debugging
        Log::info('Sepay webhook received', [
            'headers' => $request->headers->all(),
            'data' => $request->all(),
        ]);

        // Verify signature (optional - skip if webhook_secret not configured)
        $signature = $request->header('X-Sepay-Signature', '');
        if ($signature && !$this->sepayService->verifyWebhookSignature($request->getContent(), $signature)) {
            Log::warning('Sepay webhook signature verification failed', [
                'signature' => $signature,
            ]);
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $data = $request->all();

        try {
            // Process webhook and find matching payment
            $payment = $this->sepayService->processWebhook($data);

            if ($payment) {
                // Activate payment and subscription
                $sepayReference = $data['reference_number'] ?? $data['id'] ?? null;
                $this->activatePayment($payment, $sepayReference);

                Log::info('Sepay payment activated successfully', [
                    'payment_id' => $payment->id,
                    'brand_id' => $payment->brand_id,
                    'amount' => $payment->amount,
                    'sepay_reference' => $sepayReference,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Payment processed successfully',
                    'payment_id' => $payment->id
                ]);
            }

            Log::info('Sepay webhook: No matching payment found or already processed', [
                'transaction_content' => $data['transaction_content'] ?? 'N/A',
                'amount' => $data['amount_in'] ?? 0,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'No action required'
            ]);
        } catch (\Exception $e) {
            Log::error('Sepay webhook processing error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Processing error'
            ], 500);
        }
    }

    /**
     * Activate payment and subscription.
     */
    protected function activatePayment(Payment $payment, ?string $sepayReference = null): void
    {
        // Update payment
        $payment->update([
            'status' => Payment::STATUS_COMPLETED,
            'sepay_reference' => $sepayReference,
            'paid_at' => now(),
        ]);

        // Activate subscription
        $subscription = $payment->subscription;
        if ($subscription && $subscription->status === BrandSubscription::STATUS_PENDING) {
            // Get duration based on billing cycle (monthly = 30, yearly = 365)
            $durationDays = $subscription->plan->getDurationDaysForCycle($subscription->billing_cycle);

            $subscription->update([
                'status' => BrandSubscription::STATUS_ACTIVE,
                'started_at' => now(),
                'expires_at' => now()->addDays($durationDays),
                'credits_remaining' => $subscription->plan->credits,
                'credits_reset_at' => now()->addMonth(),
            ]);
        }
    }
}
