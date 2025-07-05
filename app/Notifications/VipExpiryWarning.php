<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;

class VipExpiryWarning extends Notification
{
    use Queueable;
    protected $customer;

    public function __construct($customer)
    {
        $this->customer = $customer;
    }

    public function via($notifiable)
    {
        return ['telegram'];
    }

    public function toTelegram($notifiable)
    {
        $c = $this->customer;
        $expiryDate = $c->vip_card_expires_at->format('M d, Y');
        $balance = number_format($c->vip_card_balance, 2);

        $message = "⏳ *VIP Expiry Warning* ⏳\n\n";
        $message .= "*Customer:* {$c->name}\n";
        $message .= "*Phone:* {$c->phone}\n";
        $message .= "*VIP Card Expires On:* {$expiryDate}\n";
        $message .= "*Remaining Balance:* \${$balance}";

        return TelegramMessage::create()->to(env('TELEGRAM_CHAT_ID'))->content($message);
    }
}