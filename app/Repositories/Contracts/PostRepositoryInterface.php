<?php

namespace App\Repositories\Contracts;

use App\Models\Post;

/**
 * Read/write access to posts.
 */
interface PostRepositoryInterface
{
    /**
     * @param int $categoryId Category to filter by.
     * @param int $limit Maximum number of posts to return.
     * @return Post[] Most recently published posts in the category.
     */
    public function latestByCategory(int $categoryId, int $limit): array;

    /**
     * @param int $categoryId Category to count posts for.
     */
    public function countByCategory(int $categoryId): int;

    /**
     * @param int $categoryId Category to filter by.
     * @param string $sort Sort mode: "views" for most-viewed first, anything else for most recent first.
     * @param int $offset Number of posts to skip.
     * @param int $perPage Maximum number of posts to return.
     * @return Post[] One page of posts in the category.
     */
    public function paginatedByCategory(int $categoryId, string $sort, int $offset, int $perPage): array;

    /**
     * @param string $slug Post slug to look up.
     */
    public function findBySlug(string $slug): ?Post;

    /**
     * @param int $postId Post whose view count should be incremented.
     * @return int The new view count.
     */
    public function incrementViews(int $postId): int;

    /**
     * @param Post $post Post to find related candidates for.
     * @return Post[] Posts sharing at least one category with $post, excluding $post itself.
     */
    public function findCandidatesSharingCategory(Post $post): array;
}
