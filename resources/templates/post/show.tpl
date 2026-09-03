{extends file="layouts/main.tpl"}

{block name="title"}{$post->title}{/block}

{block name="content"}
    <article class="post">
        {if $post->image}
            <img src="/images/{$post->image}" alt="{$post->title}" class="post__image">
        {/if}
        <h1>{$post->title}</h1>
        <div class="post__meta">
            <span>{$post->publishedAt->getTimestamp()|date_format:"%d.%m.%Y"}</span>
            <span>{$views} просмотров</span>
        </div>
        <p class="post__description">{$post->description}</p>
        <div class="post__content">{$post->content}</div>
    </article>

    {if $related|@count > 0}
        <section class="related-posts">
            <h2>Похожие статьи</h2>
            <div class="post-grid">
                {foreach $related as $relatedPost}
                    {include file="partials/post-card.tpl" post=$relatedPost}
                {/foreach}
            </div>
        </section>
    {/if}
{/block}
