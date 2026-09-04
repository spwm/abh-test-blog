<?php

namespace Database\Seeders;

use App\Support\SlugGenerator;
use Faker\Factory;
use PDO;

/**
 * Truncates and repopulates categories, posts, and their links with fake data.
 */
final class DatabaseSeeder
{
    /**
     * @param PDO $pdo Database connection.
     */
    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * Truncates all seeded tables and repopulates them with fresh categories and posts.
     */
    public function run(): void
    {
        $this->truncate();

        $faker = Factory::create('ru_RU');
        $slugs = new SlugGenerator();
        $images = new PlaceholderImageGenerator(__DIR__ . '/../../public/images');

        $categoryIds = (new CategorySeeder($this->pdo, $slugs, $faker))->run();
        (new PostSeeder($this->pdo, $slugs, $faker, $images))->run($categoryIds);
    }

    /**
     * Empties post_category, posts, and categories, temporarily disabling foreign key checks.
     */
    private function truncate(): void
    {
        $this->pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
        $this->pdo->exec('TRUNCATE TABLE post_category');
        $this->pdo->exec('TRUNCATE TABLE posts');
        $this->pdo->exec('TRUNCATE TABLE categories');
        $this->pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
    }
}
