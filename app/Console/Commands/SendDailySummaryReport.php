<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Customer;
use App\Models\CustomerLog;
use App\Models\DailyExpense;
use App\Notifications\DailySummaryReport;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class SendDailySummaryReport extends Command
{
    protected $signature = 'report:send-daily-summary';
    protected $description = 'Sends a daily summary report to Telegram.';

    public function handle()
    {
        $today = now()->toDateString();
        $todayFormatted = now()->format('F j, Y');

        // 1. New Customers
        $newCustomers = Customer::whereDate('created_at', $today)->get();

        // 2. Daily Expenses
        $dailyExpenses = DailyExpense::whereDate('expense_date', $today)->get();
        $totalExpense = $dailyExpenses->sum('amount');

        // 3. Customer Logs and Income
        $customerLogs = CustomerLog::where('status', 'completed')->whereDate('completed_at', $today)->get();
        $totalIncome = $customerLogs->where('is_vip_top_up', false)->where('payment_method', '!=', 'VIP Card')->sum('payment_amount');
        $totalVipTopUp = $customerLogs->where('is_vip_top_up', true)->sum('payment_amount');

        // Build the message content
        $content = "📈 *Daily Report: {$todayFormatted}* 📈\n\n";

        $content .= "*New Customers:*\n";
        if ($newCustomers->isEmpty()) {
            $content .= "- None\n";
        } else {
            foreach ($newCustomers as $customer) {
                $content .= "- {$customer->name}\n";
            }
        }
        $content .= "\n";

        $content .= "*Daily Expenses:*\n";
        if ($dailyExpenses->isEmpty()) {
            $content .= "- None\n";
        } else {
            foreach ($dailyExpenses as $expense) {
                $content .= "- {$expense->item_name}: \$" . number_format($expense->amount, 2) . "\n";
            }
        }
        $content .= "*Total Expenses:* \$" . number_format($totalExpense, 2) . "\n\n";

        $content .= "*Completed Logs:*\n";
        if ($customerLogs->isEmpty()) {
            $content .= "- None\n";
        } else {
            foreach ($customerLogs as $log) {
                $customerName = $log->customer->name ?? 'N/A';
                $payment = number_format($log->payment_amount, 2);
                $method = $log->is_vip_top_up ? 'VIP Top-Up' : $log->payment_method;
                $content .= "- {$customerName}: \${$payment} ({$method})\n";
            }
        }
        $content .= "*Total Income (Non-VIP):* \$" . number_format($totalIncome, 2) . "\n";
        $content .= "*Total VIP Top-Up:* \$" . number_format($totalVipTopUp, 2) . "\n";

        // Send notification with error handling
        try {
            Notification::route('telegram', env('TELEGRAM_CHAT_ID'))->notify(new DailySummaryReport($content));
        } catch (\Exception $e) {
            Log::error('Failed to send Telegram notification for daily summary: ' . $e->getMessage());
        }

        $this->info('Daily summary report sent successfully!');
    }
}
