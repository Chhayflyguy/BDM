<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Customer;
use App\Notifications\VipBalanceReset; // NEW
use Illuminate\Support\Facades\Notification; // NEW

class ResetExpiredVipBalances extends Command
{
    protected $signature = 'vip:reset-expired-balances';
    protected $description = 'Resets the balance of VIP cards that have expired and sends a notification.';

    public function handle()
    {
        $today = now()->toDateString();
        
        $expiredCustomers = Customer::where('vip_card_expires_at', '<', $today)
                                     ->where('vip_card_balance', '>', 0)
                                     ->get();

        foreach ($expiredCustomers as $customer) {
            $oldBalance = $customer->vip_card_balance; // Store the old balance before resetting
            
            $customer->vip_card_balance = 0;
            $customer->save();

            // Send notification with the old balance information
            Notification::route('telegram', env('TELEGRAM_CHAT_ID'))
                        ->notify(new VipBalanceReset($customer, $oldBalance));
        }

        $this->info("Reset balances for {$expiredCustomers->count()} expired VIP customers and sent notifications.");
    }
}