<?php

namespace App\Services\Seeders;

/**
 * Seeder Data Provider
 *
 * Centralized data source for database seeding
 * Provides consistent, reusable seed data for creating realistic test profiles
 */
class SeederDataProvider
{
    /**
     * Get first names
     *
     * @return array<int, string>
     */
    public static function getFirstNames(): array
    {
        return [
            'Emma', 'Sophia', 'Olivia', 'Ava', 'Isabella', 'Mia', 'Charlotte', 'Amelia', 'Harper', 'Evelyn',
            'Liam', 'Noah', 'Oliver', 'Elijah', 'James', 'Benjamin', 'Lucas', 'Henry', 'Alexander', 'Mason',
            'Luna', 'Aria', 'Chloe', 'Zoe', 'Lily', 'Emily', 'Victoria', 'Grace', 'Scarlett', 'Hannah',
            'Ethan', 'Logan', 'Jackson', 'Aiden', 'Sebastian', 'Muhammad', 'Ahmed', 'Ali', 'Omar', 'Hassan',
            'Sophie', 'Sarah', 'Jessica', 'Jennifer', 'Amanda', 'Laura', 'Michelle', 'Angela', 'Maria', 'Melissa',
            'Jacob', 'Michael', 'Daniel', 'Matthew', 'Anthony', 'Robert', 'David', 'Richard', 'Joseph', 'Charles',
            'Bella', 'Giselle', 'Jessica', 'Julia', 'Natalie', 'Rachel', 'Rebecca', 'Samantha', 'Stephanie', 'Catherine',
            'William', 'Ryan', 'Kyle', 'Kevin', 'Brian', 'George', 'Edward', 'Ronald', 'Timothy', 'Jason',
        ];
    }

    /**
     * Get last names
     *
     * @return array<int, string>
     */
    public static function getLastNames(): array
    {
        return [
            'Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Rodriguez', 'Martinez',
            'Hernandez', 'Lopez', 'Gonzalez', 'Wilson', 'Anderson', 'Thomas', 'Taylor', 'Moore', 'Jackson', 'Martin',
            'Lee', 'Perez', 'Thompson', 'White', 'Harris', 'Sanchez', 'Clark', 'Ramirez', 'Lewis', 'Robinson',
            'Walker', 'Young', 'Allen', 'King', 'Wright', 'Scott', 'Torres', 'Peterson', 'Phillips', 'Campbell',
            'Parker', 'Evans', 'Edwards', 'Collins', 'Reyes', 'Stewart', 'Morris', 'Morales', 'Murphy', 'Cook',
            'Rogers', 'Gutierrez', 'Ortiz', 'Morgan', 'Peterson', 'Cooper', 'Peterson', 'Brady', 'Gilbert', 'Sullivan',
            'Bell', 'Gomez', 'Salazar', 'Pacheco', 'Vasquez', 'Zamora', 'Chavez', 'Espinoza', 'Castillo', 'Guerrero',
        ];
    }

    /**
     * Get locations
     *
     * @return array<int, string>
     */
    public static function getLocations(): array
    {
        return [
            'New York, NY', 'Los Angeles, CA', 'Chicago, IL', 'Houston, TX', 'Phoenix, AZ',
            'Philadelphia, PA', 'San Antonio, TX', 'San Diego, CA', 'Dallas, TX', 'San Jose, CA',
            'Austin, TX', 'Jacksonville, FL', 'Seattle, WA', 'Denver, CO', 'Boston, MA',
            'Miami, FL', 'Portland, OR', 'Atlanta, GA', 'Nashville, TN', 'Detroit, MI',
            'London, UK', 'Paris, France', 'Berlin, Germany', 'Madrid, Spain', 'Amsterdam, Netherlands',
            'Tokyo, Japan', 'Sydney, Australia', 'Toronto, Canada', 'Vancouver, Canada', 'Mexico City, Mexico',
        ];
    }

    /**
     * Get bios
     *
     * @return array<int, string>
     */
    public static function getBios(): array
    {
        return [
            'Adventure seeker 🌍',
            'Coffee lover ☕',
            'Fitness enthusiast 💪',
            'Music lover 🎵',
            'Foodie 🍕',
            'Traveler 🧳',
            'Book lover 📚',
            'Dog parent 🐕',
            'Cat lover 🐱',
            'Yoga enthusiast 🧘',
            'Photographer 📸',
            'Artist 🎨',
            'Chef 👨‍🍳',
            'Gamer 🎮',
            'Sports fan ⚽',
            'Movie buff 🎬',
            'Nature lover 🌿',
            'Beach person 🏖️',
            'Mountain climber ⛰️',
            'Night owl 🦉',
            'Early bird 🐦',
            'Introvert 🤫',
            'Extrovert 🤝',
            'Homebody 🏡',
            'Let\'s grab coffee ☕',
            'Swipe right for good vibes',
            'Looking for something real',
            'Let\'s see where this goes',
            'No drama please',
            'Pet lover for life',
        ];
    }

    /**
     * Get random item from array
     *
     * @param array<mixed> $array
     * @return mixed
     */
    public static function getRandomItem(array $array): mixed
    {
        return $array[array_rand($array)];
    }

    /**
     * Get random age
     *
     * @return int
     */
    public static function getRandomAge(): int
    {
        return rand(18, 50);
    }

    /**
     * Get random picture count
     *
     * @return int
     */
    public static function getRandomPictureCount(): int
    {
        return rand(2, 3);
    }
}
