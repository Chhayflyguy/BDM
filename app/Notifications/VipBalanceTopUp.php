<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;

class VipBalanceTopUp extends Notification
{
    use Queueable;
    protected $customer;
    protected $packageName;

    public function __construct($customer, $packageName)
    {
        $this->customer = $customer;
        $this->packageName = $packageName;
    }

    public function via($notifiable)
    {
        return ['telegram'];
    }

    public function toTelegram($notifiable)
    {
        $c = $this->customer;
        $newBalance = number_format($c->vip_card_balance, 2);
        $expiryDate = $c->vip_card_expires_at ? $c->vip_card_expires_at->format('M d, Y') : 'N/A';

        $message = "💰 *VIP Balance Top-Up* 💰\n\n";
        $message .= "*Customer:* {$c->name}\n";
        $message .= "*Package:* {$this->packageName}\n";
        $message .= "*New Balance:* \${$newBalance}\n";
        $message .= "*New Expiry Date:* {$expiryDate}";

        return TelegramMessage::create()->to(env('TELEGRAM_CHAT_ID'))->content($message);
    }
}
