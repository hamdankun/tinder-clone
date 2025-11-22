<?php

namespace App\Listeners;

use App\Events\UserMatched;
use Illuminate\Queue\SerializesModels;

class SendMatchNotification
{
    use SerializesModels;

    public function handle(UserMatched $event): void
    {
        // TODO: Send match notification to both users
        // This could be email, push notification, or in-app notification
        // Example: Notification::send($users, new MatchNotification());
    }
}
