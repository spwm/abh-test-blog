<?php

namespace Tests\Unit;

use App\Support\Paginator;
use PHPUnit\Framework\TestCase;

final class PaginatorTest extends TestCase
{
    public function test_calculates_offset_for_middle_page(): void
    {
        $paginator = new Paginator(requestedPage: 2, perPage: 9, totalItems: 30);

        $this->assertSame(2, $paginator->currentPage);
        $this->assertSame(4, $paginator->totalPages);
        $this->assertSame(9, $paginator->offset);
    }

    public function test_clamps_page_above_total_pages(): void
    {
        $paginator = new Paginator(requestedPage: 99, perPage: 9, totalItems: 30);

        $this->assertSame(4, $paginator->currentPage);
        $this->assertSame(27, $paginator->offset);
    }

    public function test_clamps_page_below_one(): void
    {
        $paginator = new Paginator(requestedPage: 0, perPage: 9, totalItems: 30);

        $this->assertSame(1, $paginator->currentPage);
        $this->assertSame(0, $paginator->offset);
    }

    public function test_zero_items_produces_single_empty_page(): void
    {
        $paginator = new Paginator(requestedPage: 1, perPage: 9, totalItems: 0);

        $this->assertSame(1, $paginator->totalPages);
        $this->assertSame(0, $paginator->offset);
        $this->assertFalse($paginator->hasNext());
        $this->assertFalse($paginator->hasPrevious());
    }

    public function test_last_incomplete_page_offset(): void
    {
        $paginator = new Paginator(requestedPage: 2, perPage: 9, totalItems: 10);

        $this->assertSame(2, $paginator->totalPages);
        $this->assertSame(9, $paginator->offset);
        $this->assertFalse($paginator->hasNext());
        $this->assertTrue($paginator->hasPrevious());
    }
}
