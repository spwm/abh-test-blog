document.addEventListener('DOMContentLoaded', function () {
    const nav = document.getElementById('site-nav');
    if (!nav) {
        return;
    }

    const toggle = nav.querySelector('.site-nav__toggle');
    toggle.addEventListener('click', function () {
        const isOpen = nav.classList.toggle('is-open');
        toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });
});
