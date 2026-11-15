/*
 * Progressive enhancement for published CMS pages.
 * No framework, no build step, no network requests. If this file fails to load,
 * the page remains fully readable: only the entrance animation is skipped.
 */
(function () {
    'use strict';
    var page = document.querySelector('.cms-page');
    if (!page) { return; }

    var reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
    var targets = page.querySelectorAll('[data-cms-motion]');

    function revealAll() {
        targets.forEach(function (node) { node.classList.add('is-inview'); });
    }

    if (reduced.matches || !('IntersectionObserver' in window) || targets.length === 0) {
        revealAll();
        return;
    }

    page.setAttribute('data-cms-motion-ready', 'true');

    var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (!entry.isIntersecting) { return; }
            var node = entry.target;
            var delay = parseInt(node.getAttribute('data-cms-delay') || '0', 10);
            window.setTimeout(function () { node.classList.add('is-inview'); }, delay);
            observer.unobserve(node);
        });
    }, { rootMargin: '0px 0px -8% 0px', threshold: 0.08 });

    targets.forEach(function (node) {
        if (node.getAttribute('data-cms-motion') === 'stagger') {
            var children = node.querySelectorAll('.cms-features__item, .cms-gallery__item, .cms-courses__item, .cms-stats__item');
            children.forEach(function (child, index) {
                child.style.transitionDelay = (index * 60) + 'ms';
            });
        }
        observer.observe(node);
    });

    reduced.addEventListener('change', function (event) {
        if (event.matches) { observer.disconnect(); revealAll(); }
    });
})();
