<?php

namespace Database\Seeders;

use App\Support\SlugGenerator;
use Faker\Generator;
use PDO;

/**
 * Inserts fake posts, each assigned a placeholder image and one or two random categories.
 */
final class PostSeeder
{
    private const POST_COUNT = 40;

    /**
     * @param PDO $pdo Database connection.
     * @param SlugGenerator $slugs Slug generator used for post slugs.
     * @param Generator $faker Faker instance used for post content.
     * @param PlaceholderImageGenerator $images Placeholder image generator for post covers.
     */
    public function __construct(
        private readonly PDO $pdo,
        private readonly SlugGenerator $slugs,
        private readonly Generator $faker,
        private readonly PlaceholderImageGenerator $images,
    ) {
    }

    /**
     * Inserts POST_COUNT posts and links each to one or two of the given categories.
     *
     * @param int[] $categoryIds IDs of the categories posts may be linked to.
     */
    public function run(array $categoryIds): void
    {
        for ($i = 0; $i < self::POST_COUNT; $i++) {
            $title = ucfirst($this->faker->sentence(6));
            $slug = $this->slugs->unique($title, fn (string $slug) => $this->slugExists($slug));

            $stmt = $this->pdo->prepare(
                'INSERT INTO posts (title, slug, description, content, image, views, published_at, created_at)
                 VALUES (:title, :slug, :description, :content, :image, :views, :published_at, NOW())'
            );
            $stmt->execute(params: [
                'title' => $title,
                'slug' => $slug,
                'description' => $this->faker->sentence(20),
                'content' => implode("\n\n", $this->faker->paragraphs(5)),
                'image' => $this->images->generate($i),
                'views' => rand(0, 500),
                'published_at' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s'),
            ]);

            $postId = (int) $this->pdo->lastInsertId();
            $assigned = $this->faker->randomElements($categoryIds, rand(1, 2));

            $link = $this->pdo->prepare(
                'INSERT INTO post_category (post_id, category_id) VALUES (:post_id, :category_id)'
            );
            foreach ($assigned as $categoryId) {
                $link->execute(['post_id' => $postId, 'category_id' => $categoryId]);
            }
        }
    }

    /**
     * Checks whether a post with $slug already exists.
     *
     * @param string $slug Slug to check.
     */
    private function slugExists(string $slug): bool
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM posts WHERE slug = :slug');
        $stmt->execute(['slug' => $slug]);

        return (int) $stmt->fetchColumn() > 0;
    }
}
