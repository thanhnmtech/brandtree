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
        // Log incoming webhook
        Log::info('Sepay webhook received', $request->all());

        // Verify signature
        $signature = $request->header('X-Sepay-Signature', '');
        if (!$this->sepayService->verifyWebhookSignature($request->getContent(), $signature)) {
            Log::warning('Sepay webhook signature verification failed');
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $data = $request->all();

        // Process webhook
        $payment = $this->sepayService->processWebhook($data);

        if ($payment) {
            $this->activatePayment($payment, $data['id'] ?? $data['referenceCode'] ?? null);

            Log::info('Sepay payment activated', [
                'payment_id' => $payment->id,
                'brand_id' => $payment->brand_id,
                'amount' => $payment->amount,
            ]);

            return response()->json(['success' => true, 'message' => 'Payment processed']);
        }

        Log::info('Sepay webhook: No matching payment found');
        return response()->json(['success' => true, 'message' => 'No action required']);
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
            $subscription->update([
                'status' => BrandSubscription::STATUS_ACTIVE,
                'started_at' => now(),
                'expires_at' => now()->addDays($subscription->plan->duration_days),
                'credits_remaining' => $subscription->plan->credits,
                'credits_reset_at' => now(),
            ]);
        }
    }
}
