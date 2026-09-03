{extends file="layouts/main.tpl"}

{block name="title"}Блог{/block}

{block name="content"}
    {foreach $sections as $section}
        <section class="category-section">
            <div class="category-section__header">
                <h2>{$section.category->name}</h2>
                <a href="/category/{$section.category->slug}" class="btn">Все статьи</a>
            </div>
            {if $section.category->description}
                <p class="category-section__description">{$section.category->description}</p>
            {/if}
            <div class="post-grid">
                {foreach $section.posts as $post}
                    {include file="partials/post-card.tpl" post=$post}
                {/foreach}
            </div>
        </section>
    {foreachelse}
        <p>Пока нет ни одной категории со статьями.</p>
    {/foreach}
{/block}
