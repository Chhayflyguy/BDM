<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;

class NewCustomerCreated extends Notification
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
        $phone = $this->customer->phone ?? 'N/A';
        $vipId = $this->customer->vip_card_id ? " (VIP ID: {$this->customer->vip_card_id})" : "";

        return TelegramMessage::create()
            ->to(env('TELEGRAM_CHAT_ID'))
            ->content("✅ *New Customer Created* ✅\n\n*Name:* {$name}{$vipId}\n*Phone:* {$phone}");
    }
}