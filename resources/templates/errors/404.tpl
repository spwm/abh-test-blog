{extends file="layouts/main.tpl"}

{block name="title"}Страница не найдена{/block}

{block name="content"}
    <div class="error-page">
        <h1>404</h1>
        <p>{$message}</p>
        <a href="/" class="btn">На главную</a>
    </div>
{/block}
