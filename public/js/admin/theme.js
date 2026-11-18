
(function () {
    'use strict';

    var root = document.documentElement;
    var themeKey = 'admissions-admin-theme';
    var compactKey = 'admissions-admin-sidebar-compact';

    function readPreference(key) {
        try {
            return window.localStorage.getItem(key);
        } catch (error) {
            return null;
        }
    }

    function writePreference(key, value) {
        try {
            window.localStorage.setItem(key, value);
        } catch (error) {
            // Keep controls usable when storage is unavailable.
        }
    }

    function applyTheme(theme, persist) {
        var selected = theme === 'dark' ? 'dark' : 'light';

        root.setAttribute('data-theme', selected);

        document.querySelectorAll('[data-theme-toggle]').forEach(function (button) {
            var dark = selected === 'dark';

            button.setAttribute('aria-pressed', dark ? 'true' : 'false');
            button.setAttribute('aria-label', dark ? 'Switch to light mode' : 'Switch to dark mode');
            button.setAttribute('title', dark ? 'Switch to light mode' : 'Switch to dark mode');

            var label = button.querySelector('[data-theme-label]');

            if (label) {
                label.textContent = dark ? 'Light appearance' : 'Dark appearance';
            }
        });

        if (persist) {
            writePreference(themeKey, selected);
        }
    }

    function applyCompact(compact, persist) {
        var isCompact = Boolean(compact) &&
            window.matchMedia('(min-width: 601px)').matches;

        document.body.setAttribute(
            'data-sidebar-collapsed',
            isCompact ? 'true' : 'false'
        );

        document.querySelectorAll('[data-sidebar-toggle]').forEach(function (button) {
            button.setAttribute('aria-pressed', isCompact ? 'true' : 'false');
            button.setAttribute('aria-label', isCompact ? 'Expand sidebar' : 'Collapse sidebar');
            button.setAttribute('title', isCompact ? 'Expand sidebar' : 'Collapse sidebar');
        });

        if (persist && window.matchMedia('(min-width: 601px)').matches) {
            writePreference(compactKey, isCompact ? 'true' : 'false');
        }
    }

    applyTheme(
        readPreference(themeKey) || root.getAttribute('data-theme') || 'light',
        false
    );

    document.addEventListener('DOMContentLoaded', function () {

        // The early call above sets the theme before paint, when body controls
        // do not exist yet. Re-apply it now to synchronize their labels and ARIA state.
        applyTheme(root.getAttribute('data-theme') || 'light', false);

        document.querySelectorAll('[data-theme-toggle]').forEach(function (button) {
            button.addEventListener('click', function () {
                applyTheme(
                    root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark',
                    true
                );
            });
        });

        var savedCompact = readPreference(compactKey) === 'true';

        applyCompact(savedCompact, false);

        document.querySelectorAll('[data-sidebar-toggle]').forEach(function (button) {
            button.addEventListener('click', function () {
                applyCompact(
                    document.body.getAttribute('data-sidebar-collapsed') !== 'true',
                    true
                );
            });
        });

        window.addEventListener('resize', function () {
            if (!window.matchMedia('(min-width: 601px)').matches) {
                applyCompact(false, false);
            } else {
                applyCompact(readPreference(compactKey) === 'true', false);
            }
        }, { passive: true });
    });
})();
