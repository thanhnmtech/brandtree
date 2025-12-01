<?php

namespace App\Http\Middleware;

use App\Models\Brand;
use App\Services\CreditService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCredits
{
    public function __construct(
        protected CreditService $creditService
    ) {}

    /**
     * Handle an incoming request.
     *
     * Check if brand has enough credits before allowing action.
     * Usage: Route::middleware('check.credits:10') // requires 10 credits
     *        Route::middleware('check.credits') // requires 1 credit (default)
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  int  $requiredCredits  Number of credits required for this action
     */
    public function handle(Request $request, Closure $next, int $requiredCredits = 1): Response
    {
        // Get brand from route parameter
        $brand = $request->route('brand');

        // If brand is a string (ID or slug), resolve it
        if (is_string($brand) || is_numeric($brand)) {
            $brand = Brand::find($brand);
        }

        if (!$brand instanceof Brand) {
            return $this->denyAccess($request, 'Không tìm thấy thương hiệu.');
        }

        // Check if brand has active subscription
        $subscription = $brand->activeSubscription;

        if (!$subscription) {
            return $this->denyAccess(
                $request,
                'Thương hiệu chưa có gói dịch vụ. Vui lòng đăng ký gói để tiếp tục.',
                route('brands.subscription.create', $brand)
            );
        }

        // Check if subscription is active
        if (!$subscription->isActive()) {
            return $this->denyAccess(
                $request,
                'Gói dịch vụ đã hết hạn. Vui lòng gia hạn để tiếp tục.',
                route('brands.subscription.create', $brand)
            );
        }

        // Check if brand has enough credits
        if (!$this->creditService->hasCredits($brand, $requiredCredits)) {
            $remaining = $this->creditService->getRemainingCredits($brand);

            return $this->denyAccess(
                $request,
                "Không đủ credit. Cần {$requiredCredits} credit, còn lại {$remaining} credit. Vui lòng nâng cấp gói dịch vụ.",
                route('brands.subscription.create', $brand)
            );
        }

        // Store required credits in request for later use (e.g., after action is done)
        $request->attributes->set('required_credits', $requiredCredits);

        return $next($request);
    }

    /**
     * Deny access with appropriate response based on request type.
     */
    protected function denyAccess(Request $request, string $message, ?string $redirectUrl = null): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'redirect_url' => $redirectUrl,
            ], 402); // 402 Payment Required
        }

        $redirectUrl = $redirectUrl ?? url()->previous();

        return redirect($redirectUrl)->withErrors(['credits' => $message]);
    }
}
