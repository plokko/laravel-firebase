<?php
namespace Plokko\LaravelFirebase\Channels;

use Illuminate\Notifications\Notification;

class FcmNotificationChannel
{

    function __construct()
    {
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable,Notification $notification)
    {
        $message = $notification->toFcm($notifiable);
        $message->send();
    }
}