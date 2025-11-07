<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;
use App\Models\Booking;
use Illuminate\Support\Carbon;

class NewBookingCreated extends Notification
{
    use Queueable;

    protected Booking $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking->load(['customer', 'service']);
    }

    public function via($notifiable)
    {
        return ['telegram'];
    }

    public function toTelegram($notifiable)
    {
        $b = $this->booking;
        $customerName = $b->customer?->name ?? 'N/A';
        $customerPhone = $b->customer?->phone ?? 'N/A';
        $serviceName = $b->service?->name ?? 'N/A';
        // booking_datetime may be a string; normalize to Carbon for formatting
        $dateTimeValue = $b->booking_datetime;
        if ($dateTimeValue instanceof Carbon) {
            $dateTime = $dateTimeValue->format('Y-m-d H:i');
        } else {
            try {
                $dateTime = Carbon::parse((string) $dateTimeValue)->format('Y-m-d H:i');
            } catch (\Throwable $e) {
                $dateTime = (string) $dateTimeValue;
            }
        }
        $notes = $b->notes ? "\n*Notes:* {$b->notes}" : '';

        $message = "🆕 *New Booking Created*\n\n";
        $message .= "*Customer:* {$customerName}\n";
        $message .= "*Phone:* {$customerPhone}\n";
        $message .= "*Service:* {$serviceName}\n";
        $message .= "*When:* {$dateTime}{$notes}";

        return TelegramMessage::create()
            ->to(env('TELEGRAM_CHAT_ID'))
            ->content($message);
    }
}
