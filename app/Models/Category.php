<?php

namespace App\Models;

/**
 * Immutable blog category.
 */
final class Category
{
    /**
     * @param int $id Primary key.
     * @param string $name Display name.
     * @param string $slug URL-friendly identifier, unique across categories.
     * @param string|null $description Optional longer description.
     */
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly ?string $description,
    ) {
    }
}
