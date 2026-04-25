<?php

namespace App\Services;

use App\Helpers\OrderHelper;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Class OrderService
 * Service xử lý toàn bộ quy trình mua hàng
 *
 * Thay thế logic bị duplicate ở:
 * - CheckoutController: processDomain, processHosting, processVPS, processSourceCode
 * - AjaxController: buyDomain, buyHosting, buyVPS, buySourceCode
 * - DomainController: buy()
 * - DomainService: purchaseDomain()
 *
 * Quy trình chuẩn: Validate → Check availability → Get price → Check balance
 *                  → DB transaction (createOrder + deductBalance) → Notify → Return
 */
class OrderService
{
    protected TelegramService $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    /**
     * Xử lý toàn bộ quy trình mua hàng
     *
     * @param string $productType Loại sản phẩm ('domain', 'hosting', 'vps', 'sourcecode')
     * @param int    $productId   ID sản phẩm (0 nếu không dùng, như domain chỉ dùng domain name)
     * @param int    $userId      ID người dùng
     * @param array  $options     Dữ liệu bổ sung:
     *                            - Domain: [domain, ns1, ns2, hsd]
     *                            - Hosting/VPS: [period]
     *                            - SourceCode: []
     * @return array ['success' => bool, 'message' => string, 'mgd' => string|null, 'order' => Model|null]
     */
    public function placeOrder(string $productType, int $productId, int $userId, array $options = []): array
    {
        try {
            // 1. Lấy strategy tương ứng với loại sản phẩm
            $strategy = OrderStrategyFactory::make($productType);

            // 2. Validate dữ liệu đầu vào
            $validationResult = $strategy->validate($productId, $options);
            if ($validationResult !== true) {
                return [
                    'success' => false,
                    'message' => $validationResult,
                    'mgd'     => null,
                    'order'   => null,
                ];
            }

            // 3. Kiểm tra tính khả dụng của sản phẩm (domain chưa được mua, sản phẩm tồn tại...)
            $availabilityResult = $strategy->checkAvailability($productId, $options);
            if ($availabilityResult !== true) {
                return [
                    'success' => false,
                    'message' => $availabilityResult,
                    'mgd'     => null,
                    'order'   => null,
                ];
            }

            // 4. Lấy giá sản phẩm
            $price = $strategy->getPrice($productId, $options);
            if ($price === null || $price < 0) {
                return [
                    'success' => false,
                    'message' => 'Không thể xác định giá sản phẩm. Vui lòng thử lại!',
                    'mgd'     => null,
                    'order'   => null,
                ];
            }

            // 5. Lấy thông tin user và kiểm tra số dư
            $user = User::find($userId);
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'Không tìm thấy thông tin tài khoản!',
                    'mgd'     => null,
                    'order'   => null,
                ];
            }

            // 6. Tạo mã giao dịch duy nhất
            $mgd = OrderHelper::generateMGD();

            // 7. Thực hiện trong transaction để đảm bảo atomic
            DB::beginTransaction();

            // 7a. Xử lý Voucher (nếu có)
            $discount = 0;
            $voucherCode = $options['voucher'] ?? null;
            if ($voucherCode) {
                $voucher = \App\Models\Voucher::where('code', $voucherCode)
                    ->where(function($query) use ($userId) {
                        $query->whereNull('user_id')->orWhere('user_id', $userId);
                    })
                    ->first();

                if ($voucher) {
                    // Kiểm tra hết hạn
                    if ($voucher->expires_at && \Carbon\Carbon::parse($voucher->expires_at)->isPast()) {
                        throw new \Exception("Mã giảm giá này đã hết hạn sử dụng!");
                    }

                    // Xử lý theo loại Voucher
                    if (is_null($voucher->user_id)) {
                        // VOUCHER CHUNG (Global)
                        $alreadyUsed = \App\Models\VoucherUsage::where('user_id', $userId)
                            ->where('voucher_id', $voucher->id)
                            ->exists();
                        
                        if ($alreadyUsed) {
                            throw new \Exception("Bạn đã sử dụng mã giảm giá này cho một đơn hàng trước đó rồi!");
                        }

                        // Ghi nhận sử dụng
                        \App\Models\VoucherUsage::create([
                            'user_id'    => $userId,
                            'voucher_id' => $voucher->id
                        ]);
                        
                        Log::info("OrderService: Global Voucher [{$voucherCode}] recorded for User [{$userId}]");
                    } else {
                        // VOUCHER CÁ NHÂN
                        if ($voucher->is_used) {
                            throw new \Exception("Mã giảm giá này đã được sử dụng!");
                        }

                        $voucher->is_used = 1;
                        $voucher->save();
                        
                        Log::info("OrderService: Personal Voucher [{$voucherCode}] marked as USED");
                    }

                    $discount = $voucher->value;
                } else {
                    Log::warning("OrderService: Voucher [{$voucherCode}] not found or not belonging to User [{$userId}]");
                    throw new \Exception("Mã giảm giá không hợp lệ hoặc không thuộc sở hữu của bạn!");
                }
            }

            // Tính toán lại giá cuối cùng
            $finalPrice = max(0, $price - $discount);

            // KIỂM TRA SỐ DƯ SAU KHI ĐÃ GIẢM GIÁ
            if ((int)$user->tien < $finalPrice) {
                DB::rollBack();
                Log::warning("OrderService: Insufficient balance after discount. User {$userId} has {$user->tien}, needs {$finalPrice}");
                return [
                    'success' => false,
                    'message' => 'Số dư tài khoản không đủ (ngay cả khi đã dùng Voucher)! Vui lòng nạp thêm tiền.',
                    'mgd'     => null,
                    'order'   => null,
                ];
            }

            // 7b. Tạo đơn hàng
            $order = $strategy->createOrder($userId, $productId, $mgd, $options, $finalPrice);

            // 7c. Trừ số dư tài khoản người mua (dùng giá đã giảm)
            $user->incrementBalance(-1 * $finalPrice);

            // 7c. Xử lý Hoa hồng Affiliate (Tính trên giá cuối cùng người dùng trả)
            if ($user->referrer_id) {
                $referrer = User::find($user->referrer_id);
                if ($referrer) {
                    $commissionRate = 0.05; // 5%
                    $commission = (int)($finalPrice * $commissionRate);
                    
                    if ($commission > 0) {
                        $referrer->incrementBalance($commission);
                        Log::info("Affiliate Commission: User {$userId} bought product, credited {$commission} to referrer {$referrer->id}");
                        
                        // Thông báo Telegram cho Người giới thiệu (Optional)
                        try {
                            $this->telegramService->sendMessage($referrer->id, 
                                "💰 *Bạn nhận được hoa hồng!*\n\n" .
                                "Cấp dưới `{$user->taikhoan}` vừa mua hàng.\n" .
                                "Số tiền hoa hồng: `+" . number_format($commission) . "₫`"
                            );
                        } catch (\Exception $e) { /* Ignore telegram error */ }
                    }
                }
            }

            DB::commit();

            // 8. Gửi thông báo Telegram (không ảnh hưởng đến kết quả transaction)
            try {
                $telegramData = $strategy->getTelegramData($userId, $productId, $mgd, $options);
                $this->telegramService->notifyNewOrder($productType, $telegramData);
            } catch (\Exception $e) {
                // Không throw - lỗi Telegram không nên làm thất bại đơn hàng
                Log::warning('OrderService: Telegram notification failed', [
                    'product_type' => $productType,
                    'mgd'          => $mgd,
                    'error'        => $e->getMessage(),
                ]);
            }

            // 9. Gửi email xác nhận (không ảnh hưởng kết quả)
            try {
                if (!empty($user->email)) {
                    // OrderConfirmationMail nhận (object $order, string $productType, User $user, array $data)
                    // Bỏ qua nếu Mail class chưa được cập nhật
                    Mail::to($user->email)->send(
                        new \App\Mail\OrderConfirmationMail($order, $strategy->getProductTypeName(), $user, [])
                    );
                }
            } catch (\Exception $e) {
                Log::warning('OrderService: Email confirmation failed', [
                    'product_type' => $productType,
                    'mgd'          => $mgd,
                    'user_email'   => $user->email,
                    'error'        => $e->getMessage(),
                ]);
            }

            // AI Security Observer: Purchase Event
            (new \App\Services\AISecurityService())->observe('PURCHASE_SUCCESS', [
                'user_id' => $userId,
                'product_type' => $productType,
                'product_id' => $productId,
                'price' => $price,
                'mgd' => $mgd
            ]);

            Log::info('OrderService: Order placed successfully', [
                'product_type' => $productType,
                'product_id'   => $productId,
                'user_id'      => $userId,
                'mgd'          => $mgd,
                'price'        => $price,
            ]);

            return [
                'success' => true,
                'message' => 'Đặt hàng thành công! ' . ($productType === 'sourcecode'
                    ? 'Bạn có thể tải xuống ngay.'
                    : 'Vui lòng chờ admin xét duyệt.'),
                'mgd'     => $mgd,
                'order'   => $order,
            ];

        } catch (\InvalidArgumentException $e) {
            // Product type không hợp lệ
            Log::error('OrderService: Invalid product type', [
                'product_type' => $productType,
                'error'        => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Loại sản phẩm không hợp lệ!',
                'mgd'     => null,
                'order'   => null,
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('OrderService: Order placement failed', [
                'product_type' => $productType,
                'product_id'   => $productId,
                'user_id'      => $userId,
                'error'        => $e->getMessage(),
                'trace'        => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra khi đặt hàng. Vui lòng thử lại!',
                'mgd'     => null,
                'order'   => null,
            ];
        }
    }

    /**
     * Lấy giá sản phẩm (dùng cho trang checkout hiển thị giá)
     */
    public function getProductPrice(string $productType, int $productId, array $options = []): ?int
    {
        try {
            $strategy = OrderStrategyFactory::make($productType);
            return $strategy->getPrice($productId, $options);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Kiểm tra sản phẩm có khả dụng không (dùng trước khi hiển thị trang checkout)
     */
    public function checkProductAvailability(string $productType, int $productId, array $options = []): bool|string
    {
        try {
            $strategy = OrderStrategyFactory::make($productType);
            return $strategy->checkAvailability($productId, $options);
        } catch (\InvalidArgumentException $e) {
            return 'Loại sản phẩm không hợp lệ!';
        }
    }
}
