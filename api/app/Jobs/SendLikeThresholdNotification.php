<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Repositories\Contracts\LikeRepositoryContract;
use App\Mail\LikeThresholdReachedMail;
use Illuminate\Support\Facades\Mail;

class SendLikeThresholdNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $userId,
    ) {}

    public function handle(LikeRepositoryContract $likeRepository): void
    {
        $user = User::find($this->userId);
        if (!$user) {
            return;
        }

        // Count likes received by this user
        $likeCount = $likeRepository->countLikesReceived($this->userId);

        // Only send email if threshold is reached (50+)
        if ($likeCount >= 50) {
            // Send email to admin with user details
            $adminEmail = env('ADMIN_EMAIL', 'admin@tinder-clone.local');
            
            Mail::to($adminEmail)->send(
                new LikeThresholdReachedMail($user, $likeCount)
            );
        }
    }
}
