<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\BrandSubscription;
use App\Models\Payment;
use App\Services\SepayService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(
        protected SepayService $sepayService
    ) {}

    /**
     * Show payment form for a subscription.
     */
    public function create(Request $request, Brand $brand): View|RedirectResponse
    {
        $this->authorize('update', $brand);

        $subscriptionId = $request->query('subscription');
        $subscription = BrandSubscription::where('id', $subscriptionId)
            ->where('brand_id', $brand->id)
            ->where('status', BrandSubscription::STATUS_PENDING)
            ->with('plan')
            ->first();

        if (!$subscription) {
            return redirect()->route('brands.subscription.show', $brand)
                ->withErrors(['subscription' => __('messages.subscription.not_found')]);
        }

        return view('brands.payments.create', compact('brand', 'subscription'));
    }

    /**
     * Process payment - Create payment and show bank transfer info.
     */
    public function store(Request $request, Brand $brand): RedirectResponse
    {
        $this->authorize('update', $brand);

        $validated = $request->validate([
            'subscription_id' => 'required|exists:brand_subscriptions,id',
        ]);

        $subscription = BrandSubscription::where('id', $validated['subscription_id'])
            ->where('brand_id', $brand->id)
            ->where('status', BrandSubscription::STATUS_PENDING)
            ->with('plan')
            ->first();

        if (!$subscription) {
            return back()->withErrors(['subscription' => __('messages.subscription.not_found')]);
        }

        // Check if there's already a pending payment
        $existingPayment = Payment::where('subscription_id', $subscription->id)
            ->where('status', Payment::STATUS_PENDING)
            ->first();

        if ($existingPayment) {
            return redirect()->route('brands.payments.show', [$brand, $existingPayment]);
        }

        // Create payment record with price based on billing cycle
        $amount = $subscription->plan->getPriceForCycle($subscription->billing_cycle);

        $payment = Payment::create([
            'brand_id' => $brand->id,
            'subscription_id' => $subscription->id,
            'amount' => $amount,
            'payment_method' => Payment::METHOD_BANK_TRANSFER,
            'status' => Payment::STATUS_PENDING,
        ]);

        // Generate payment code
        $paymentCode = $this->sepayService->generatePaymentCode($payment);
        $payment->update(['transaction_id' => $paymentCode]);

        return redirect()->route('brands.payments.show', [$brand, $payment])
            ->with('info', __('messages.payment.transfer_info'));
    }

    /**
     * Show payment status/details with bank transfer info.
     */
    public function show(Brand $brand, Payment $payment): View|RedirectResponse
    {
        $this->authorize('view', $brand);

        if ($payment->brand_id !== $brand->id) {
            abort(404);
        }

        $payment->load('subscription.plan');

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
            ->with('subscription.plan')
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
        $payment = $payment->fresh(['subscription.plan']);

        if (!$payment->isPending()) {
            return response()->json([
                'status' => $payment->status,
                'completed' => true,
                'message' => 'Thanh toán đã được xử lý',
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
