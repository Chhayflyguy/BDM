<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;

class VipPaymentMade extends Notification
{
    use Queueable;
    protected $log;

    public function __construct($log)
    {
        $this->log = $log;
    }

    public function via($notifiable)
    {
        return ['telegram'];
    }

    public function toTelegram($notifiable)
    {
        $log = $this->log;
        $customer = $log->customer;
        $cost = number_format($log->total_cost, 2);
        $remainingBalance = number_format($customer->vip_card_balance, 2);

        $message = "💳 *VIP Card Payment* 💳\n\n";
        $message .= "*Customer:* {$customer->name}\n";
        $message .= "*Phone:* {$customer->phone}\n";
        $message .= "*Remaining Balance:* \${$remainingBalance}";

        return TelegramMessage::create()->to(env('TELEGRAM_CHAT_ID'))->content($message);
    }
}