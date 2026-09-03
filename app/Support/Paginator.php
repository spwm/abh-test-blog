<?php

namespace App\Support;

/**
 * Computes page bounds and offsets for a paginated result set.
 */
final class Paginator
{
    public readonly int $currentPage;
    public readonly int $totalPages;
    public readonly int $offset;

    /**
     * @param int $requestedPage Page requested by the caller; clamped to [1, totalPages].
     * @param int $perPage Number of items per page.
     * @param int $totalItems Total number of items across all pages.
     */
    public function __construct(int $requestedPage, public readonly int $perPage, public readonly int $totalItems)
    {
        $this->totalPages = $totalItems > 0 ? (int) ceil($totalItems / $perPage) : 1;
        $this->currentPage = max(1, min($requestedPage, $this->totalPages));
        $this->offset = ($this->currentPage - 1) * $perPage;
    }

    /**
     * @return bool Whether a page before the current one exists.
     */
    public function hasPrevious(): bool
    {
        return $this->currentPage > 1;
    }

    /**
     * @return bool Whether a page after the current one exists.
     */
    public function hasNext(): bool
    {
        return $this->currentPage < $this->totalPages;
    }
}
