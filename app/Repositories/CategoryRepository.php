<?php

namespace App\Repositories;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use PDO;

/**
 * PDO-backed implementation of CategoryRepositoryInterface.
 */
final class CategoryRepository implements CategoryRepositoryInterface
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * @return Category[] Categories that have at least one post, ordered by name.
     */
    public function withPosts(): array
    {
        $stmt = $this->pdo->query(
            'SELECT DISTINCT c.* FROM categories c
             INNER JOIN post_category pc ON pc.category_id = c.id
             ORDER BY c.name ASC'
        );

        return array_map($this->hydrate(...), $stmt->fetchAll());
    }

    /**
     * @param string $slug Category slug to look up.
     */
    public function findBySlug(string $slug): ?Category
    {
        $stmt = $this->pdo->prepare('SELECT * FROM categories WHERE slug = :slug LIMIT 1');
        $stmt->execute(['slug' => $slug]);
        $row = $stmt->fetch();

        return $row ? $this->hydrate($row) : null;
    }

    /**
     * Maps a raw database row to a Category model.
     *
     * @param array<string, mixed> $row
     */
    private function hydrate(array $row): Category
    {
        return new Category(
            id: (int) $row['id'],
            name: $row['name'],
            slug: $row['slug'],
            description: $row['description'],
        );
    }
}
