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
        $c = $this->customer;
        $message = "✅ *New Customer Profile Created* ✅\n\n";
        $message .= "*Name:* {$c->name}\n";
        $message .= "*Phone:* {$c->phone}\n";
        $message .= "*Gender:* {$c->gender}\n";
        $message .= "*Age:* {$c->age}\n\n";

        if ($c->vip_card_id) {
            $message .= "*VIP Card ID:* {$c->vip_card_id}\n";
            $message .= "*Package:* {$c->vip_card_type}\n";
            $message .= "*Initial Balance:* $" . number_format($c->vip_card_balance, 2) . "\n";
            $message .= "*Expires On:* " . ($c->vip_card_expires_at ? $c->vip_card_expires_at->format('M d, Y') : 'N/A') . "\n";
        }

        return TelegramMessage::create()->to(env('TELEGRAM_CHAT_ID'))->content($message);
    }
}