<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BayarGgService
{
    private string $apiKey;
    private string $baseUrl;
    private string $paymentMethod;

    public function __construct()
    {
        $this->apiKey        = config('bayargg.api_key');
        $this->baseUrl       = config('bayargg.base_url');
        $this->paymentMethod = config('bayargg.payment_method');
    }

    // =========================================================================
    // CREATE PAYMENT
    // =========================================================================

    /**
     * Buat payment baru di bayar.gg
     *
     * @param  int    $amount       Nominal dalam rupiah
     * @param  string $description  Deskripsi pembayaran
     * @param  array  $customer     ['name', 'email', 'phone']
     * @param  string $orderId      Order ID internal kita (untuk referensi)
     * @return array|null
     */
    public function createPayment(
        int $amount,
        string $description,
        array $customer = [],
        string $orderId = ''
    ): ?array {
        try {
            $payload = [
                'amount'         => $amount,
                'description'    => $description,
                'payment_method' => $this->paymentMethod,
                'callback_url'   => route('payment.callback'),
                'redirect_url'   => route('payment.success') . '?order=' . $orderId,
            ];

            if (!empty($customer['name']))  $payload['customer_name']  = $customer['name'];
            if (!empty($customer['email'])) $payload['customer_email'] = $customer['email'];
            if (!empty($customer['phone'])) $payload['customer_phone'] = $customer['phone'];

            $response = Http::withHeaders([
                'X-API-Key'    => $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ])->post($this->baseUrl . '/create-payment.php', $payload);

            $data = $response->json();

            if ($response->successful() && ($data['success'] ?? false)) {
                return $data;
            }

            Log::error('BayarGg createPayment failed', [
                'status'  => $response->status(),
                'payload' => $payload,
                'response'=> $data,
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('BayarGg createPayment exception: ' . $e->getMessage());
            return null;
        }
    }

    // =========================================================================
    // CHECK PAYMENT
    // =========================================================================

    /**
     * Cek status pembayaran berdasarkan invoice_id bayar.gg
     *
     * @param  string $invoiceId  Invoice ID dari bayar.gg (PAY-xxx)
     * @return array|null
     */
    public function checkPayment(string $invoiceId): ?array
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
            ])->get($this->baseUrl . '/check-payment.php', [
                'invoice' => $invoiceId,
            ]);

            $data = $response->json();

            if ($response->successful() && ($data['success'] ?? false)) {
                return $data;
            }

            Log::warning('BayarGg checkPayment failed', [
                'invoice'  => $invoiceId,
                'response' => $data,
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('BayarGg checkPayment exception: ' . $e->getMessage());
            return null;
        }
    }

    // =========================================================================
    // VERIFY CALLBACK
    // =========================================================================

    /**
     * Verifikasi callback dari bayar.gg
     * Bayar.gg mengirim POST request dengan body JSON ke callback_url
     *
     * @param  array $payload  Request payload dari bayar.gg
     * @return bool
     */
    public function verifyCallback(array $payload): bool
    {
        // Verifikasi dengan double-check ke API
        $invoiceId = $payload['invoice_id'] ?? null;

        if (!$invoiceId) return false;

        $payment = $this->checkPayment($invoiceId);

        if (!$payment) return false;

        // Pastikan status paid dan amount cocok
        return $payment['status'] === 'paid';
    }

    // =========================================================================
    // GET PAYMENT METHODS
    // =========================================================================

    /**
     * Ambil metode pembayaran yang tersedia
     */
    public function getPaymentMethods(): ?array
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
            ])->get($this->baseUrl . '/get-payment-methods.php');

            $data = $response->json();

            return ($response->successful() && ($data['success'] ?? false)) ? $data : null;

        } catch (\Exception $e) {
            Log::error('BayarGg getPaymentMethods exception: ' . $e->getMessage());
            return null;
        }
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    /**
     * Cek apakah status dari callback adalah "paid"
     */
    public function isPaid(array $callbackData): bool
    {
        return ($callbackData['status'] ?? '') === 'paid';
    }

    /**
     * Format nominal ke rupiah
     */
    public function formatRupiah(int $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}
