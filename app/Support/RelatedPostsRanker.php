<?php

namespace App\Support;

use App\Models\Post;

/**
 * Ranks posts by relevance to a target post based on shared categories.
 */
final class RelatedPostsRanker
{
    /**
     * Returns posts related to $target, ranked by shared-category count (desc)
     * then by publish date (desc), excluding $target itself and unrelated posts.
     *
     * @param Post $target Post to find related posts for.
     * @param Post[] $candidates Pool of posts to rank.
     * @param int $limit Maximum number of related posts to return.
     * @return Post[] Ranked, deduplicated related posts, truncated to $limit.
     */
    public function rank(Post $target, array $candidates, int $limit): array
    {
        $scored = $this->scoreCandidates($target, $candidates);

        usort($scored, $this->compareByRelevance(...));

        return array_map(
            fn (array $row) => $row['post'],
            array_slice($scored, 0, $limit)
        );
    }

    /**
     * Filters out $target and unrelated candidates, pairing the rest with their shared-category count.
     *
     * @param Post[] $candidates
     * @return array<int, array{post: Post, shared: int}>
     */
    private function scoreCandidates(Post $target, array $candidates): array
    {
        $scored = [];

        foreach ($candidates as $candidate) {
            if ($candidate->id === $target->id) {
                continue;
            }

            $shared = $this->sharedCategoryCount($target, $candidate);
            if ($shared === 0) {
                continue;
            }

            $scored[] = ['post' => $candidate, 'shared' => $shared];
        }

        return $scored;
    }

    private function sharedCategoryCount(Post $a, Post $b): int
    {
        return count(array_intersect($a->categoryIds, $b->categoryIds));
    }

    /**
     * Comparator for usort: higher shared-category count first, ties broken by more recent publishedAt.
     *
     * @param array{post: Post, shared: int} $a
     * @param array{post: Post, shared: int} $b
     */
    private function compareByRelevance(array $a, array $b): int
    {
        if ($a['shared'] !== $b['shared']) {
            return $b['shared'] <=> $a['shared'];
        }

        return $b['post']->publishedAt <=> $a['post']->publishedAt;
    }
}
