{if $paginator->totalPages > 1}
<nav class="pagination">
    {if $paginator->hasPrevious()}
        <a href="/category/{$categorySlug}?sort={$sort}&page={$paginator->currentPage - 1}" class="pagination__link">&laquo; Назад</a>
    {/if}
    <span class="pagination__status">Страница {$paginator->currentPage} из {$paginator->totalPages}</span>
    {if $paginator->hasNext()}
        <a href="/category/{$categorySlug}?sort={$sort}&page={$paginator->currentPage + 1}" class="pagination__link">Вперёд &raquo;</a>
    {/if}
</nav>
{/if}
