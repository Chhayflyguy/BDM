<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;

class VipBalanceTopUp extends Notification
{
    use Queueable;

    protected $customer;
    protected $amount;

    public function __construct($customer, $amount)
    {
        $this->customer = $customer;
        $this->amount = $amount;
    }

    public function via($notifiable)
    {
        return ['telegram'];
    }

    public function toTelegram($notifiable)
    {
        $name = $this->customer->name;
        $newBalance = number_format($this->customer->vip_card_balance, 2);

        return TelegramMessage::create()
            ->to(env('TELEGRAM_CHAT_ID'))
            ->content("💰 *VIP Balance Top-Up* 💰\n\n*Customer:* {$name}\n*Amount Added:* \${$this->amount}\n*New Balance:* \${$newBalance}");
    }
}
