<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{block name="title"}Блог{/block}</title>
    <link rel="stylesheet" href="/css/main.css">
</head>
<body>
    {include file="partials/header.tpl"}
    <main class="container">
        {block name="content"}{/block}
    </main>
    {include file="partials/footer.tpl"}
    <script src="/js/main.js" defer></script>
</body>
</html>
