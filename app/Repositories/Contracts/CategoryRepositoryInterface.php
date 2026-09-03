<?php

namespace App\Repositories\Contracts;

use App\Models\Category;

/**
 * Read access to categories.
 */
interface CategoryRepositoryInterface
{
    /**
     * @return Category[] Categories that have at least one post, ordered by name.
     */
    public function withPosts(): array;

    /**
     * @param string $slug Category slug to look up.
     */
    public function findBySlug(string $slug): ?Category;
}
