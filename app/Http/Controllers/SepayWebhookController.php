<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\PlanService;
use App\Services\SepayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SepayWebhookController extends Controller
{
    public function __construct(
        protected SepayService $sepayService,
        protected PlanService $planService
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
                // Activate payment and create subscription
                $sepayReference = $data['reference_number'] ?? $data['id'] ?? null;
                $subscription = $this->activatePayment($payment, $sepayReference);

                Log::info('Sepay payment activated successfully', [
                    'payment_id' => $payment->id,
                    'brand_id' => $payment->brand_id,
                    'amount' => $payment->amount,
                    'subscription_id' => $subscription?->id,
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
     * Activate payment and create subscription.
     */
    protected function activatePayment(Payment $payment, ?string $sepayReference = null)
    {
        // Update sepay reference
        if ($sepayReference) {
            $payment->update(['sepay_reference' => $sepayReference]);
        }

        // Use PlanService to activate payment and create subscription
        return $this->planService->activatePayment($payment);
    }
}
