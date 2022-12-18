<?php

namespace App\Notifications;

use NotificationChannels\ExpoPushNotifications\ExpoChannel;
use NotificationChannels\ExpoPushNotifications\ExpoMessage;
use Illuminate\Notifications\Notification;

class ActivityCreated extends Notification
{
    public function via($notifiable)
    {
        return [ExpoChannel::class];
    }

    public function toExpoPush($notifiable)
    {
        return ExpoMessage::create()
            ->enableSound()
            ->title("Pemberitahuan")
            ->body("Aktivitas baru telah ditambahkan");
    }
}
