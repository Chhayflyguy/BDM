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
        $name = $this->customer->name;
        $expiryDate = $this->customer->vip_card_expires_at->format('M d, Y');

        return TelegramMessage::create()
            ->to(env('TELEGRAM_CHAT_ID'))
            ->content("⏳ *VIP Expiry Warning* ⏳\n\n*Customer:* {$name}\n*VIP Card Expires On:* {$expiryDate}");
    }
}