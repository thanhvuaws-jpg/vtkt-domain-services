<?php

namespace App\Http\Controllers;

use App\Services\BankingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    protected $bankingService;

    public function __construct(BankingService $bankingService)
    {
        $this->bankingService = $bankingService;
    }

    /**
     * Webhook nhận dữ liệu từ Ngân hàng (SePay, Casso, v.v.)
     */
    public function bankWebhook(Request $request)
    {
        $data = $request->all();
        $headers = $request->headers->all();

        Log::info("Banking Webhook Received:", ['data' => $data]);

        $result = $this->bankingService->handleWebhook('BANKING', $data, $headers);

        if ($result['success']) {
            return response()->json(['status' => 'success', 'message' => 'Processed']);
        }

        return response()->json(['status' => 'error', 'message' => $result['message']], 400);
    }

    /**
     * Webhook nhận dữ liệu từ Momo
     */
    public function momoWebhook(Request $request)
    {
        $data = $request->all();
        $headers = $request->headers->all();

        Log::info("Momo Webhook Received:", ['data' => $data]);

        $result = $this->bankingService->handleWebhook('MOMO', $data, $headers);

        if ($result['success']) {
            return response()->json(['status' => 'success', 'message' => 'Processed']);
        }

        return response()->json(['status' => 'error', 'message' => $result['message']], 400);
    }
}
