<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;

class VipBalanceReset extends Notification
{
    use Queueable;

    protected $customer;
    protected $oldBalance;

    /**
     * Create a new notification instance.
     */
    public function __construct($customer, $oldBalance)
    {
        $this->customer = $customer;
        $this->oldBalance = $oldBalance;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['telegram'];
    }

    /**
     * Get the Telegram representation of the notification.
     */
    public function toTelegram(object $notifiable): TelegramMessage
    {
        $c = $this->customer;
        $balance = number_format($this->oldBalance, 2);

        $message = "🚫 *VIP Balance Reset* 🚫\n\n";
        $message .= "*Customer:* {$c->name}\n";
        $message .= "*Reason:* Card Expired\n";
        $message .= "*Previous Balance:* \${$balance} has been reset to $0.00.";

        return TelegramMessage::create()->to(env('TELEGRAM_CHAT_ID'))->content($message);
    }
}