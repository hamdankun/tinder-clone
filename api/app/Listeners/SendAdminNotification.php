<?php

namespace App\Listeners;

use App\Events\LikeThresholdReached;
use App\Jobs\SendLikeThresholdNotification;
use Illuminate\Queue\SerializesModels;

class SendAdminNotification
{
    use SerializesModels;

    public function handle(LikeThresholdReached $event): void
    {
        // Dispatch job to send admin notification
        SendLikeThresholdNotification::dispatch($event->userId);
    }
}
