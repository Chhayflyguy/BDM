<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Customer;

class ResetExpiredVipBalances extends Command
{
    protected $signature = 'vip:reset-expired-balances';
    protected $description = 'Resets the balance of VIP cards that have expired.';

    public function handle()
    {
        $today = now()->toDateString();
        
        $expiredCustomers = Customer::where('vip_card_expires_at', '<', $today)
                                     ->where('vip_card_balance', '>', 0)
                                     ->get();

        foreach ($expiredCustomers as $customer) {
            $customer->vip_card_balance = 0;
            $customer->save();
        }

        $this->info("Reset balances for {$expiredCustomers->count()} expired VIP customers.");
    }
}
