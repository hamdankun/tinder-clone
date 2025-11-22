<?php

namespace App\Services;

use App\Models\Picture;
use App\Repositories\Contracts\PictureRepositoryContract;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Picture Service
 * Handles business logic for picture uploads, storage, and management
 */
class PictureService
{
    public function __construct(
        private PictureRepositoryContract $pictureRepository
    ) {}

    /**
     * Upload a picture for a user
     *
     * @param int $userId User ID who is uploading the picture
     * @param UploadedFile $file Uploaded file (already validated by controller)
     * @param bool $isPrimary Whether this should be the primary picture
     * @return Picture The created picture
     */
    public function uploadPicture(int $userId, UploadedFile $file, bool $isPrimary = false): Picture
    {
        // Store file in storage/app/public/pictures/{userId}/
        $path = $file->store(
            "pictures/{$userId}",
            'public'
        );

        // If this is primary, unset other primary pictures
        if ($isPrimary) {
            $this->unsetPrimaryPictures($userId);
        }

        // Get next order number
        $order = $this->getNextPictureOrder($userId);

        // Create picture record in database
        $picture = $this->pictureRepository->create([
            'user_id' => $userId,
            'url' => Storage::url($path),
            'is_primary' => $isPrimary,
            'order' => $order,
        ]);

        return $picture;
    }

    /**
     * Delete a picture
     *
     * @param int $pictureId Picture ID to delete
     * @param int $userId User ID (for authorization check)
     * @return bool
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function deletePicture(int $pictureId, int $userId): bool
    {
        $picture = $this->pictureRepository->findById($pictureId);

        if (!$picture || $picture->user_id !== $userId) {
            throw new \Exception('Picture not found or unauthorized');
        }

        // Delete file from storage
        if ($picture->url) {
            $this->deleteStorageFile($picture->url);
        }

        // Delete record from database
        return $this->pictureRepository->delete($pictureId);
    }

    /**
     * Set a picture as primary
     *
     * @param int $pictureId Picture ID to set as primary
     * @param int $userId User ID (for authorization check)
     * @return Picture
     */
    public function setPrimaryPicture(int $pictureId, int $userId): Picture
    {
        $picture = $this->pictureRepository->findById($pictureId);

        if (!$picture || $picture->user_id !== $userId) {
            throw new \Exception('Picture not found or unauthorized');
        }

        // Unset other primary pictures for this user
        $this->unsetPrimaryPictures($userId);

        // Set this picture as primary
        return $this->pictureRepository->update($pictureId, ['is_primary' => true]);
    }

    /**
     * Get all pictures for a user
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserPictures(int $userId)
    {
        return $this->pictureRepository->getByUserId($userId);
    }

    /**
     * Reorder pictures for a user
     *
     * @param int $userId
     * @param array $pictureIds Array of picture IDs in new order
     * @return bool
     */
    public function reorderPictures(int $userId, array $pictureIds): bool
    {
        // Verify all pictures belong to this user
        $pictures = $this->pictureRepository->getByUserId($userId);
        $userPictureIds = $pictures->pluck('id')->toArray();

        foreach ($pictureIds as $pictureId) {
            if (!in_array($pictureId, $userPictureIds)) {
                throw new \Exception('Invalid picture ID for this user');
            }
        }

        // Update order
        foreach ($pictureIds as $index => $pictureId) {
            $this->pictureRepository->update($pictureId, ['order' => $index + 1]);
        }

        return true;
    }

    /**
     * Unset all primary pictures for a user
     *
     * @param int $userId
     * @return void
     */
    private function unsetPrimaryPictures(int $userId): void
    {
        Picture::where('user_id', $userId)
            ->where('is_primary', true)
            ->update(['is_primary' => false]);
    }

    /**
     * Get the next order number for a user's pictures
     *
     * @param int $userId
     * @return int
     */
    private function getNextPictureOrder(int $userId): int
    {
        $lastOrder = Picture::where('user_id', $userId)
            ->max('order') ?? 0;

        return $lastOrder + 1;
    }

    /**
     * Delete file from storage
     *
     * @param string $url
     * @return void
     */
    private function deleteStorageFile(string $url): void
    {
        // Extract path from URL (e.g., /storage/pictures/1/abc123.jpg -> pictures/1/abc123.jpg)
        if (str_contains($url, '/storage/')) {
            $path = str_replace('/storage/', '', $url);
            Storage::disk('public')->delete($path);
        }
    }
}
