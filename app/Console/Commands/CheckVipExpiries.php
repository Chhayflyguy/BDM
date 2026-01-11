<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Customer;
use App\Notifications\VipExpiryWarning;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class CheckVipExpiries extends Command
{
    protected $signature = 'vip:check-expiries';
    protected $description = 'Check for VIP cards expiring in 15 days and send a notification.';

    public function handle()
    {
        $datesToCheck = [
            now()->addDays(15)->toDateString(),
            now()->addDays(7)->toDateString(),
        ];

        $expiringCustomers = Customer::whereIn('vip_card_expires_at', $datesToCheck)
            ->where('vip_card_balance', '>=', 100)
            ->get();

        foreach ($expiringCustomers as $customer) {
            try {
                Notification::route('telegram', env('TELEGRAM_CHAT_ID'))->notify(new VipExpiryWarning($customer));
            } catch (\Exception $e) {
                Log::error('Failed to send Telegram notification for VIP expiry warning: ' . $e->getMessage());
            }
        }

        $this->info("Checked for VIP expiries. Found {$expiringCustomers->count()} warnings to send.");
    }
}
