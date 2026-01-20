<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Payment;
use App\Services\SepayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(
        protected SepayService $sepayService
    ) {}

    /**
     * Show payment status/details with bank transfer info.
     */
    public function show(Brand $brand, Payment $payment): View|RedirectResponse
    {
        $this->authorize('view', $brand);

        if ($payment->brand_id !== $brand->id) {
            abort(404);
        }

        $payment->load('plan');

        // Generate payment code if not exists
        if ($payment->isPending() && !$payment->transaction_id) {
            $paymentCode = $this->sepayService->generatePaymentCode($payment);
            $payment->update(['transaction_id' => $paymentCode]);
        }

        // Get bank transfer info for pending payments
        $bankInfo = null;
        if ($payment->isPending()) {
            $bankInfo = $this->sepayService->getBankTransferInfo($payment);
        }

        return view('brands.payments.show', compact('brand', 'payment', 'bankInfo'));
    }

    /**
     * Show payment history for a brand.
     */
    public function index(Brand $brand): View
    {
        $this->authorize('view', $brand);

        $payments = Payment::where('brand_id', $brand->id)
            ->with(['plan', 'subscription.plan'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('brands.payments.index', compact('brand', 'payments'));
    }

    /**
     * Check payment status via Sepay API (form submit).
     */
    public function checkStatus(Brand $brand, Payment $payment): RedirectResponse
    {
        $this->authorize('view', $brand);

        if ($payment->brand_id !== $brand->id) {
            abort(404);
        }

        if (!$payment->isPending()) {
            return back()->with('info', __('messages.payment.already_processed'));
        }

        $transaction = $this->sepayService->checkTransaction($payment->transaction_id);

        if ($transaction && ($transaction['transferAmount'] ?? 0) >= $payment->amount) {
            return back()->with('success', __('messages.payment.success'));
        }

        return back()->with('info', __('messages.payment.not_received'));
    }

    /**
     * Check payment status via AJAX (for auto-polling).
     */
    public function checkStatusAjax(Brand $brand, Payment $payment): JsonResponse
    {
        $this->authorize('view', $brand);

        if ($payment->brand_id !== $brand->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Reload payment from database to get latest status
        $payment = $payment->fresh(['plan', 'subscription.plan']);

        if (!$payment->isPending()) {
            return response()->json([
                'status' => $payment->status,
                'completed' => true,
                // 'message' => 'Thanh toán đã được xử lý',
                'redirect' => route('brands.subscription.show', $brand)
            ]);
        }

        return response()->json([
            'status' => $payment->status,
            'completed' => false,
            'message' => 'Đang chờ thanh toán'
        ]);
    }
}
