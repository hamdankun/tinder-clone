<?php

namespace Database\Seeders;

use App\Models\Picture;
use App\Models\User;
use App\Services\Seeders\PictureSeederService;
use App\Services\Seeders\SeederDataProvider;
use App\Services\Seeders\UserSeederService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Psr\Log\LoggerInterface;

/**
 * Database Seeder for Tinder Clone
 *
 * Orchestrates seeding of dummy users and their profile pictures
 * Uses service classes for separation of concerns and reusability
 * Creates 30 users with 2-3 pictures each from Lorem Picsum API
 */
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    private const SEED_USER_COUNT = 30;

    public function __construct(
        private UserSeederService $userSeederService,
        private PictureSeederService $pictureSeederService,
        private LoggerInterface $logger,
    ) {}

    /**
     * Seed the application's database
     *
     * @return void
     */
    public function run(): void
    {
        $this->clearExistingData();
        $this->seedUsers();
        $this->displayResults();
    }

    /**
     * Clear existing data from database
     *
     * @return void
     */
    private function clearExistingData(): void
    {
        Picture::truncate();
        User::truncate();

        $this->command->line('<info>Cleared existing users and pictures</info>');
    }

    /**
     * Seed users with their pictures
     *
     * @return void
     */
    private function seedUsers(): void
    {
        // Pass command to picture seeder for detailed logging
        $this->pictureSeederService->setCommand($this->command);

        for ($i = 1; $i <= self::SEED_USER_COUNT; $i++) {
            $user = $this->userSeederService->createSeedUser($i);

            $this->command->line("<fg=cyan>➜</> User #{$i}: {$user->name} ({$user->email})");

            $pictureCount = SeederDataProvider::getRandomPictureCount();
            $createdPictures = $this->pictureSeederService->createSeedPictures($user, $pictureCount);

            $this->command->line("  <fg=yellow>→</> {$createdPictures}/{$pictureCount} pictures created");
            $this->command->line('');

            $this->displayProgressIfNeeded($i);
        }
    }

    /**
     * Display progress every 10 users
     *
     * @param int $index
     * @return void
     */
    private function displayProgressIfNeeded(int $index): void
    {
        if ($index % 10 === 0) {
            $this->command->info("✅ Created {$index} users with pictures...");
        }
    }

    /**
     * Display final results
     *
     * @return void
     */
    private function displayResults(): void
    {
        $userCount = User::count();
        $pictureCount = Picture::count();

        $this->command->line('');
        $this->command->info('✅ Database seeding completed successfully!');
        $this->command->line("<comment>Total users created:</comment> <info>{$userCount}</info>");
        $this->command->line("<comment>Total pictures created:</comment> <info>{$pictureCount}</info>");
    }
}
