<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use NotificationChannels\ExpoPushNotifications\ExpoChannel;
use NotificationChannels\ExpoPushNotifications\ExpoMessage;

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
            ->title('Pemberitahuan')
            ->body('Aktivitas baru telah ditambahkan');
    }
}
