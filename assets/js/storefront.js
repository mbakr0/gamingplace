(function () {
    'use strict';

    function ready(callback) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', callback);
        } else {
            callback();
        }
    }

    ready(function () {
        var navToggle = document.querySelector('[data-nav-toggle]');
        var siteNav = document.getElementById('siteNav');

        if (navToggle && siteNav) {
            navToggle.addEventListener('click', function () {
                var isOpen = navToggle.getAttribute('aria-expanded') === 'true';
                navToggle.setAttribute('aria-expanded', String(!isOpen));
                navToggle.setAttribute('aria-label', isOpen ? 'Open navigation' : 'Close navigation');
                siteNav.classList.toggle('is-open', !isOpen);
            });

            siteNav.querySelectorAll('a').forEach(function (link) {
                link.addEventListener('click', function () {
                    navToggle.setAttribute('aria-expanded', 'false');
                    navToggle.setAttribute('aria-label', 'Open navigation');
                    siteNav.classList.remove('is-open');
                });
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    navToggle.setAttribute('aria-expanded', 'false');
                    navToggle.setAttribute('aria-label', 'Open navigation');
                    siteNav.classList.remove('is-open');
                }
            });
        }

        document.querySelectorAll('[data-dismiss-alert]').forEach(function (button) {
            button.addEventListener('click', function () {
                var alert = button.closest('.store-alert');
                if (alert) alert.remove();
            });
        });

        document.querySelectorAll('[data-open-account]').forEach(function (link) {
            link.addEventListener('click', function (event) {
                var account = document.querySelector('.account-popover');
                if (!account) return;
                event.preventDefault();
                account.open = true;
                var username = document.getElementById('nav-username');
                if (username) username.focus();
                account.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            });
        });

        var grid = document.getElementById('gamesGrid');
        var searchInput = document.getElementById('gameSearch');
        var sortSelect = document.getElementById('gameSort');
        var filterButtons = Array.prototype.slice.call(document.querySelectorAll('[data-price-filter]'));
        var noResults = document.getElementById('noSearchResults');
        var activePriceFilter = 'all';

        if (grid) {
            var cards = Array.prototype.slice.call(grid.querySelectorAll('.game-card-wrap'));

            function cardMatchesPrice(card) {
                var price = Number(card.getAttribute('data-price') || 0);
                if (activePriceFilter === 'under20') return price < 20;
                if (activePriceFilter === '20to40') return price >= 20 && price <= 40;
                if (activePriceFilter === 'over40') return price > 40;
                return true;
            }

            function applyFilters() {
                var query = searchInput ? searchInput.value.trim().toLocaleLowerCase() : '';
                var visibleCount = 0;

                cards.forEach(function (card) {
                    var title = card.getAttribute('data-game-title') || '';
                    var matches = (!query || title.indexOf(query) !== -1) && cardMatchesPrice(card);
                    card.hidden = !matches;
                    if (matches) visibleCount += 1;
                });

                if (noResults) noResults.hidden = visibleCount > 0 || cards.length === 0;
            }

            function sortCards() {
                if (!sortSelect) return;
                var sortBy = sortSelect.value;
                var ordered = cards.slice().sort(function (a, b) {
                    var priceA = Number(a.getAttribute('data-price') || 0);
                    var priceB = Number(b.getAttribute('data-price') || 0);
                    var titleA = a.getAttribute('data-game-title') || '';
                    var titleB = b.getAttribute('data-game-title') || '';

                    if (sortBy === 'price-low') return priceA - priceB;
                    if (sortBy === 'price-high') return priceB - priceA;
                    if (sortBy === 'title') return titleA.localeCompare(titleB);
                    return Number(a.getAttribute('data-original-order') || 0) - Number(b.getAttribute('data-original-order') || 0);
                });

                ordered.forEach(function (card) {
                    grid.appendChild(card);
                });
                cards = ordered;
                applyFilters();
            }

            if (searchInput) {
                searchInput.addEventListener('input', applyFilters);
            }

            filterButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    activePriceFilter = button.getAttribute('data-price-filter') || 'all';
                    filterButtons.forEach(function (item) {
                        var selected = item === button;
                        item.classList.toggle('is-active', selected);
                        item.setAttribute('aria-pressed', String(selected));
                    });
                    applyFilters();
                });
            });

            if (sortSelect) sortSelect.addEventListener('change', sortCards);

            document.querySelectorAll('[data-clear-filters]').forEach(function (button) {
                button.addEventListener('click', function () {
                    activePriceFilter = 'all';
                    if (searchInput) searchInput.value = '';
                    filterButtons.forEach(function (item) {
                        var selected = item.getAttribute('data-price-filter') === 'all';
                        item.classList.toggle('is-active', selected);
                        item.setAttribute('aria-pressed', String(selected));
                    });
                    if (sortSelect) sortSelect.value = 'featured';
                    sortCards();
                });
            });

            applyFilters();
        }

        document.querySelectorAll('.add-game-form, .details-buy-form').forEach(function (form) {
            form.addEventListener('submit', function () {
                var button = form.querySelector('button[type="submit"]');
                if (!button) return;
                button.disabled = true;
                button.classList.add('is-loading');
                if (button.classList.contains('add-to-bag')) {
                    var label = button.querySelector('span');
                    if (label) label.textContent = 'Adding';
                }
            });
        });

        document.querySelectorAll('img').forEach(function (image) {
            image.addEventListener('error', function () {
                image.classList.add('image-load-failed');
                image.setAttribute('aria-hidden', 'true');
                image.alt = '';
            });
        });
    });
})();
