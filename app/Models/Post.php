<?php

namespace App\Models;

use DateTimeImmutable;

/**
 * Immutable blog post.
 */
final class Post
{
    /**
     * @param int $id Primary key.
     * @param string $title Post title.
     * @param string $slug URL-friendly identifier, unique across posts.
     * @param string $description Short summary shown in listings.
     * @param string $content Full post body.
     * @param string|null $image Filename of the cover image, if any.
     * @param int $views Number of times the post has been viewed.
     * @param DateTimeImmutable $publishedAt Publication timestamp.
     * @param int[] $categoryIds IDs of the categories this post belongs to.
     */
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $slug,
        public readonly string $description,
        public readonly string $content,
        public readonly ?string $image,
        public readonly int $views,
        public readonly DateTimeImmutable $publishedAt,
        public readonly array $categoryIds = [],
    ) {
    }
}
