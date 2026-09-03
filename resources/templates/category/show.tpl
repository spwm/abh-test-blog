{extends file="layouts/main.tpl"}

{block name="title"}{$category->name}{/block}

{block name="content"}
    <h1>{$category->name}</h1>
    {if $category->description}
        <p class="category-description">{$category->description}</p>
    {/if}

    <div class="sort-switch">
        <a href="/category/{$category->slug}?sort=date" class="{if $sort == 'date'}is-active{/if}">По дате</a>
        <a href="/category/{$category->slug}?sort=views" class="{if $sort == 'views'}is-active{/if}">По просмотрам</a>
    </div>

    <div class="post-grid">
        {foreach $posts as $post}
            {include file="partials/post-card.tpl" post=$post}
        {foreachelse}
            <p>В этой категории пока нет статей.</p>
        {/foreach}
    </div>

    {include file="partials/pagination.tpl" paginator=$paginator sort=$sort categorySlug=$category->slug}
{/block}
