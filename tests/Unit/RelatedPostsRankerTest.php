<?php

namespace Tests\Unit;

use App\Models\Post;
use App\Support\RelatedPostsRanker;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class RelatedPostsRankerTest extends TestCase
{
    private function makePost(int $id, array $categoryIds, string $publishedAt): Post
    {
        return new Post(
            id: $id,
            title: "Post {$id}",
            slug: "post-{$id}",
            description: 'desc',
            content: 'content',
            image: null,
            views: 0,
            publishedAt: new DateTimeImmutable($publishedAt),
            categoryIds: $categoryIds,
        );
    }

    public function test_ranks_by_number_of_shared_categories(): void
    {
        $ranker = new RelatedPostsRanker();
        $target = $this->makePost(1, [10, 20], '2026-01-01');

        $oneShared = $this->makePost(2, [10], '2026-01-05');
        $twoShared = $this->makePost(3, [10, 20], '2026-01-02');

        $result = $ranker->rank($target, [$oneShared, $twoShared], limit: 3);

        $this->assertSame([3, 2], array_map(fn (Post $p) => $p->id, $result));
    }

    public function test_breaks_ties_by_most_recent_published_at(): void
    {
        $ranker = new RelatedPostsRanker();
        $target = $this->makePost(1, [10], '2026-01-01');

        $older = $this->makePost(2, [10], '2026-01-01');
        $newer = $this->makePost(3, [10], '2026-02-01');

        $result = $ranker->rank($target, [$older, $newer], limit: 3);

        $this->assertSame([3, 2], array_map(fn (Post $p) => $p->id, $result));
    }

    public function test_excludes_target_post_and_unrelated_candidates(): void
    {
        $ranker = new RelatedPostsRanker();
        $target = $this->makePost(1, [10], '2026-01-01');

        $self = $this->makePost(1, [10], '2026-01-01');
        $unrelated = $this->makePost(4, [99], '2026-01-01');

        $result = $ranker->rank($target, [$self, $unrelated], limit: 3);

        $this->assertSame([], $result);
    }

    public function test_respects_limit(): void
    {
        $ranker = new RelatedPostsRanker();
        $target = $this->makePost(1, [10], '2026-01-01');

        $candidates = [
            $this->makePost(2, [10], '2026-01-02'),
            $this->makePost(3, [10], '2026-01-03'),
            $this->makePost(4, [10], '2026-01-04'),
        ];

        $result = $ranker->rank($target, $candidates, limit: 2);

        $this->assertCount(2, $result);
        $this->assertSame([4, 3], array_map(fn (Post $p) => $p->id, $result));
    }
}
