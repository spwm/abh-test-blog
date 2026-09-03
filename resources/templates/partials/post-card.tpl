<article class="post-card">
    {if $post->image}
        <a href="/post/{$post->slug}" class="post-card__image">
            <img src="/images/{$post->image}" alt="{$post->title}">
        </a>
    {/if}
    <div class="post-card__body">
        <h3 class="post-card__title"><a href="/post/{$post->slug}">{$post->title}</a></h3>
        <p class="post-card__description">{$post->description}</p>
        <div class="post-card__meta">
            <span>{$post->publishedAt->getTimestamp()|date_format:"%d.%m.%Y"}</span>
            <span>{$post->views} просмотров</span>
        </div>
    </div>
</article>
