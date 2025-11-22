<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Mail\HighLikeCountNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;

class CheckHighLikeCount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'likes:check-high-count';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for users with like count exceeding threshold and send admin notification';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $threshold = (int) config('services.like_threshold', 50);
        $adminEmail = config('services.admin_email');

        if (!$adminEmail) {
            $this->error('ADMIN_EMAIL environment variable not set');
            return self::FAILURE;
        }

        $this->info("Checking for users with more than {$threshold} likes...");

        // Get users with like count exceeding threshold
        // Only notify about users we haven't notified yet today
        $users = User::withCount('likedBy')
            ->having('liked_by_count', '>=', $threshold)
            ->get();

        if ($users->isEmpty()) {
            $this->info('No users exceeding the like threshold.');
            return self::SUCCESS;
        }

        $this->info("Found {$users->count()} user(s) with {$threshold}+ likes");

        foreach ($users as $user) {
            // Use cache to prevent duplicate emails within 24 hours
            $cacheKey = "like_notification_sent_{$user->id}";

            if (!Cache::has($cacheKey)) {
                try {
                    Mail::to($adminEmail)->send(
                        new HighLikeCountNotification(
                            $user,
                            $user->liked_by_count,
                            $threshold,
                        )
                    );

                    // Cache this notification for 24 hours
                    Cache::put($cacheKey, true, now()->addDay());

                    $this->line(
                        "✓ Notification sent for {$user->name} "
                        . "({$user->liked_by_count} likes) to {$adminEmail}"
                    );
                } catch (\Exception $e) {
                    $this->error(
                        "Failed to send notification for {$user->name}: "
                        . $e->getMessage()
                    );
                }
            } else {
                $this->line(
                    "⊘ Notification already sent today for {$user->name} "
                    . "({$user->liked_by_count} likes)"
                );
            }
        }

        $this->info('Completed checking high like counts');
        return self::SUCCESS;
    }
}
