<?php

namespace Database\Seeders;

use App\Support\SlugGenerator;
use Faker\Generator;
use PDO;

/**
 * Inserts a fixed list of blog categories with generated slugs and fake descriptions.
 */
final class CategorySeeder
{
    private const NAMES = [
        'Frontend', 'Backend', 'DevOps', 'Базы данных', 'Тестирование', 'Мобильная разработка',
    ];

    /**
     * @param PDO $pdo Database connection.
     * @param SlugGenerator $slugs Slug generator used for category slugs.
     * @param Generator $faker Faker instance used for category descriptions.
     */
    public function __construct(private PDO $pdo, private SlugGenerator $slugs, private Generator $faker)
    {
    }

    /**
     * Inserts all categories.
     *
     * @return int[] IDs of the inserted categories, in insertion order.
     */
    public function run(): array
    {
        $ids = [];
        foreach (self::NAMES as $name) {
            $stmt = $this->pdo->prepare(
                'INSERT INTO categories (name, slug, description, created_at)
                 VALUES (:name, :slug, :description, NOW())'
            );
            $stmt->execute([
                'name' => $name,
                'slug' => $this->slugs->generate($name),
                'description' => $this->faker->sentence(12),
            ]);
            $ids[] = (int) $this->pdo->lastInsertId();
        }

        return $ids;
    }
}
