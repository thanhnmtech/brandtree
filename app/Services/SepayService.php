<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SepayService
{
    protected string $apiKey;
    protected string $merchantId;
    protected string $baseUrl;
    protected string $bankAccountNumber;
    protected string $bankName;
    protected string $bankAccountName;

    public function __construct()
    {
        $this->apiKey = config('services.sepay.api_key');
        $this->merchantId = config('services.sepay.merchant_id');
        $this->baseUrl = config('services.sepay.base_url');
        $this->bankAccountNumber = config('services.sepay.bank_account_number');
        $this->bankName = config('services.sepay.bank_name');
        $this->bankAccountName = config('services.sepay.bank_account_name');
    }

    /**
     * Generate unique payment code for tracking
     */
    public function generatePaymentCode(Payment $payment): string
    {
        return 'BT'.str_pad($payment->id, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Get bank transfer information for QR code
     */
    public function getBankTransferInfo(Payment $payment): array
    {
        $paymentCode = $this->generatePaymentCode($payment);

        return [
            'bank_name' => $this->bankName,
            'account_number' => $this->bankAccountNumber,
            'account_name' => $this->bankAccountName,
            'amount' => $payment->amount,
            'content' => $paymentCode,
            'qr_url' => $this->generateQRUrl($payment->amount, $paymentCode),
        ];
    }

    /**
     * Generate VietQR URL for bank transfer
     */
    public function generateQRUrl(int $amount, string $content): string
    {
        // VietQR format: https://img.vietqr.io/image/{bankCode}-{accountNumber}-{template}.png?amount={amount}&addInfo={content}
        $bankCode = $this->getBankCode($this->bankName);

        return "https://img.vietqr.io/image/{$bankCode}-{$this->bankAccountNumber}-compact2.png"
             . "?amount={$amount}&addInfo=" . urlencode($content)
             . "&accountName=" . urlencode($this->bankAccountName);
    }

    /**
     * Get bank code for VietQR
     */
    protected function getBankCode(string $bankName): string
    {
        $bankCodes = [
            'MB Bank' => 'MB',
            'Vietcombank' => 'VCB',
            'Techcombank' => 'TCB',
            'BIDV' => 'BIDV',
            'Agribank' => 'AGR',
            'VPBank' => 'VPB',
            'TPBank' => 'TPB',
            'ACB' => 'ACB',
            'Sacombank' => 'STB',
            'HDBank' => 'HDB',
        ];

        return $bankCodes[$bankName] ?? 'MB';
    }

    /**
     * Check transaction status via Sepay API
     */
    public function checkTransaction(string $paymentCode): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->get($this->baseUrl . '/transactions/list', [
                'account_number' => $this->bankAccountNumber,
                'transaction_content' => $paymentCode,
                'limit' => 1,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data['transactions'])) {
                    return $data['transactions'][0];
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Sepay API error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Verify webhook signature
     */
    public function verifyWebhookSignature(string $payload, string $signature): bool
    {
        $secret = config('services.sepay.webhook_secret');
        if (!$secret) {
            return true; // Skip verification if no secret configured
        }

        $expectedSignature = hash_hmac('sha256', $payload, $secret);
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Process webhook data
     */
    public function processWebhook(array $data): ?Payment
    {
        $content = $data['content'] ?? $data['transaction_content'] ?? '';

        // Extract payment code from content (format: BT0001P000001)
        if (preg_match('/BT(\d{4})P(\d{6})/', strtoupper($content), $matches)) {
            $brandId = (int) $matches[1];
            $paymentId = (int) $matches[2];

            $payment = Payment::where('id', $paymentId)
                ->where('brand_id', $brandId)
                ->where('status', Payment::STATUS_PENDING)
                ->first();

            if ($payment && $data['transferAmount'] >= $payment->amount) {
                return $payment;
            }
        }

        return null;
    }
}
