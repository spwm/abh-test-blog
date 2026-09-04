<?php

namespace App\Repositories;

use Exception;
use App\Models\Post;
use App\Repositories\Contracts\PostRepositoryInterface;
use DateTimeImmutable;
use PDO;

/**
 * PDO-backed implementation of PostRepositoryInterface.
 */
final class PostRepository implements PostRepositoryInterface
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * @param int $categoryId Category to filter by.
     * @param int $limit Maximum number of posts to return.
     * @return Post[] Most recently published posts in the category.
     */
    public function latestByCategory(int $categoryId, int $limit): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT p.* FROM posts p
             INNER JOIN post_category pc ON pc.post_id = p.id
             WHERE pc.category_id = :category_id
             ORDER BY p.published_at DESC
             LIMIT :limit'
        );
        $stmt->bindValue('category_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return array_map($this->hydrate(...), $stmt->fetchAll());
    }

    /**
     * @param int $categoryId Category to count posts for.
     */
    public function countByCategory(int $categoryId): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM post_category WHERE category_id = :category_id');
        $stmt->execute(['category_id' => $categoryId]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * @param int $categoryId Category to filter by.
     * @param string $sort Sort mode: "views" for most-viewed first, anything else for most recent first.
     * @param int $offset Number of posts to skip.
     * @param int $perPage Maximum number of posts to return.
     * @return Post[] One page of posts in the category.
     */
    public function paginatedByCategory(int $categoryId, string $sort, int $offset, int $perPage): array
    {
        $orderBy = $sort === 'views' ? 'p.views DESC' : 'p.published_at DESC';

        $stmt = $this->pdo->prepare(
            "SELECT p.* FROM posts p
             INNER JOIN post_category pc ON pc.post_id = p.id
             WHERE pc.category_id = :category_id
             ORDER BY {$orderBy}
             LIMIT :limit OFFSET :offset"
        );
        $stmt->bindValue('category_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindValue('limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return array_map($this->hydrate(...), $stmt->fetchAll());
    }

    /**
     * @param string $slug Post slug to look up.
     * @throws Exception
     */
    public function findBySlug(string $slug): ?Post
    {
        $stmt = $this->pdo->prepare('SELECT * FROM posts WHERE slug = :slug LIMIT 1');
        $stmt->execute(['slug' => $slug]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return $this->hydrate($row, $this->categoryIdsForPost((int) $row['id']));
    }

    /**
     * @param int $postId Post whose view count should be incremented.
     * @return int The new view count.
     */
    public function incrementViews(int $postId): int
    {
        $stmt = $this->pdo->prepare('UPDATE posts SET views = views + 1 WHERE id = :id');
        $stmt->execute(['id' => $postId]);

        $stmt = $this->pdo->prepare('SELECT views FROM posts WHERE id = :id');
        $stmt->execute(['id' => $postId]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * @param Post $post Post to find related candidates for.
     * @return Post[] Posts sharing at least one category with $post, excluding $post itself.
     * @throws Exception
     */
    public function findCandidatesSharingCategory(Post $post): array
    {
        if ($post->categoryIds === []) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($post->categoryIds), '?'));
        $stmt = $this->pdo->prepare(
            "SELECT DISTINCT p.* FROM posts p
             INNER JOIN post_category pc ON pc.post_id = p.id
             WHERE pc.category_id IN ({$placeholders}) AND p.id != ?"
        );
        $stmt->execute([...$post->categoryIds, $post->id]);

        $rows = $stmt->fetchAll();
        if ($rows === []) {
            return [];
        }

        $categoryIdsByPostId = $this->categoryIdsForPosts(array_column($rows, 'id'));

        return array_map(
            fn (array $row) => $this->hydrate($row, $categoryIdsByPostId[(int) $row['id']] ?? []),
            $rows
        );
    }

    /**
     * @param int $postId Post to look up category IDs for.
     * @return int[] IDs of the categories this post belongs to.
     */
    private function categoryIdsForPost(int $postId): array
    {
        return $this->categoryIdsForPosts([$postId])[$postId] ?? [];
    }

    /**
     * Looks up category IDs for several posts in a single query, avoiding one query per post.
     *
     * @param int[] $postIds Post IDs to look up category IDs for.
     * @return array<int, int[]> Category IDs, keyed by post ID.
     */
    private function categoryIdsForPosts(array $postIds): array
    {
        $placeholders = implode(',', array_fill(0, count($postIds), '?'));

        $stmt = $this->pdo->prepare(
            "SELECT post_id, category_id FROM post_category WHERE post_id IN ({$placeholders})"
        );

        $stmt->execute(array_map(intval(...), $postIds));

        $categoryIdsByPostId = [];
        foreach ($stmt->fetchAll() as $link) {
            $categoryIdsByPostId[(int) $link['post_id']][] = (int) $link['category_id'];
        }

        return $categoryIdsByPostId;
    }

    /**
     * Maps a raw database row to a Post model.
     *
     * @param array<string, mixed> $row
     * @param int[] $categoryIds
     * @throws Exception
     */
    private function hydrate(array $row, array $categoryIds = []): Post
    {
        return new Post(
            id: (int) $row['id'],
            title: $row['title'],
            slug: $row['slug'],
            description: $row['description'],
            content: $row['content'],
            image: $row['image'],
            views: (int) $row['views'],
            publishedAt: new DateTimeImmutable($row['published_at']),
            categoryIds: $categoryIds,
        );
    }
}
