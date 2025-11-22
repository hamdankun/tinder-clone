<?php

namespace App\Services\Seeders;

use App\Models\Picture;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Psr\Log\LoggerInterface;

/**
 * Picture Seeder Service
 *
 * Handles picture creation for database seeding
 * Downloads images from Lorem Picsum API and stores them locally
 */
class PictureSeederService
{
    private const PICSUM_BASE_URL = 'https://picsum.photos/seed';
    private const IMAGE_SIZE = '800';
    private const DOWNLOAD_DELAY = 1000;

    private ?Command $command = null;

    public function __construct(
        private LoggerInterface $logger,
    ) {}

    /**
     * Set command for output logging
     *
     * @param Command $command
     * @return self
     */
    public function setCommand(Command $command): self
    {
        $this->command = $command;

        return $this;
    }

    /**
     * Create seed pictures for a user
     *
     * @param User $user
     * @param int $pictureCount Number of pictures to create (2-3)
     * @return int Number of pictures successfully created
     */
    public function createSeedPictures(User $user, int $pictureCount = 3): int
    {
        $successCount = 0;

        for ($i = 1; $i <= $pictureCount; $i++) {
            if ($this->downloadAndStorePicture($user, $i)) {
                $successCount++;
                $this->logPictureSuccess($user, $i);
            } else {
                $this->logPictureFailure($user, $i);
            }

            // Add delay to avoid rate limiting
            usleep(self::DOWNLOAD_DELAY);
        }

        return $successCount;
    }

    /**
     * Download image from Lorem Picsum and store locally
     *
     * @param User $user
     * @param int $pictureIndex
     * @return bool True if successful, false otherwise
     */
    private function downloadAndStorePicture(User $user, int $pictureIndex): bool
    {
        try {
            $imageContent = $this->downloadImage($user->id, $pictureIndex);

            if (!$imageContent) {
                return false;
            }

            $this->storeImageAndCreateRecord($user, $imageContent, $pictureIndex);

            return true;
        } catch (\Exception $e) {
            $this->logger->warning(
                "Failed to download image for user {$user->id}: " . $e->getMessage()
            );

            return false;
        }
    }

    /**
     * Download image from Lorem Picsum API
     *
     * @param int $userId
     * @param int $pictureIndex
     * @return string|false Image content or false on failure
     */
    private function downloadImage(int $userId, int $pictureIndex): string|false
    {
        $seed = "tinder-clone-user-{$userId}-pic-{$pictureIndex}";
        $url = self::PICSUM_BASE_URL . '/' . urlencode($seed) . '/' . self::IMAGE_SIZE . '/' . self::IMAGE_SIZE;

        return @file_get_contents($url);
    }

    /**
     * Store image locally and create database record
     *
     * @param User $user
     * @param string $imageContent
     * @param int $pictureIndex
     * @return void
     */
    private function storeImageAndCreateRecord(User $user, string $imageContent, int $pictureIndex): void
    {
        $filename = "picture_{$user->id}_{$pictureIndex}.jpg";
        $path = "pictures/{$user->id}/{$filename}";

        Storage::disk('public')->put($path, $imageContent);

        Picture::create([
            'user_id' => $user->id,
            'url' => Storage::url($path),
            'is_primary' => $pictureIndex === 1,
            'order' => $pictureIndex,
        ]);
    }

    /**
     * Log successful picture creation
     *
     * @param User $user
     * @param int $pictureIndex
     * @return void
     */
    private function logPictureSuccess(User $user, int $pictureIndex): void
    {
        if ($this->command) {
            $this->command->line("  <fg=green>✓</> Picture {$pictureIndex} created for {$user->name}");
        }

        $this->logger->info(
            "Picture {$pictureIndex} successfully created for user {$user->id} ({$user->name})"
        );
    }

    /**
     * Log failed picture creation
     *
     * @param User $user
     * @param int $pictureIndex
     * @return void
     */
    private function logPictureFailure(User $user, int $pictureIndex): void
    {
        if ($this->command) {
            $this->command->line("  <fg=red>✗</> Picture {$pictureIndex} failed for {$user->name}");
        }

        $this->logger->warning(
            "Picture {$pictureIndex} failed for user {$user->id} ({$user->name})"
        );
    }
}
