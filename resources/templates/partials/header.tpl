<header class="site-header">
    <div class="container">
        <a class="site-header__logo" href="/">Главная</a>
        <nav class="site-nav" id="site-nav">
            <button
                type="button"
                class="site-nav__toggle"
                aria-expanded="false"
                aria-controls="site-nav-list"
                aria-label="Открыть меню"
            >
                <span class="site-nav__toggle-bar"></span>
                <span class="site-nav__toggle-bar"></span>
                <span class="site-nav__toggle-bar"></span>
            </button>
            <ul class="site-nav__list" id="site-nav-list">
                {foreach $navCategories as $navCategory}
                    <li><a href="/category/{$navCategory->slug}">{$navCategory->name}</a></li>
                {/foreach}
            </ul>
        </nav>
    </div>
</header>
