/**
 * Скрипты админ-панели (Clean Card UI).
 * — сайдбар на мобильных
 * — inline-подтверждение удаления в CRUD
 */
(function () {
    'use strict';

    function initSidebarToggle() {
        var body = document.body;
        var sidenav = document.getElementById('sidenav-main');
        var toggler = document.getElementById('iconNavbarSidenav');
        var closer = document.getElementById('iconSidenav');

        function closeSidebar() {
            body.classList.remove('sidenav-open');
        }

        function toggleSidebar(e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            body.classList.toggle('sidenav-open');
        }

        if (toggler) {
            toggler.addEventListener('click', toggleSidebar);
        }

        if (closer) {
            closer.addEventListener('click', function (e) {
                e.stopPropagation();
                closeSidebar();
            });
        }

        document.addEventListener('click', function (e) {
            if (!body.classList.contains('sidenav-open') || !sidenav) {
                return;
            }
            if (sidenav.contains(e.target) || (toggler && toggler.contains(e.target))) {
                return;
            }
            closeSidebar();
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeSidebar();
            }
        });
    }

    function closeAllDeleteConfirms(except) {
        document.querySelectorAll('[data-cc-crud-actions].is-confirming').forEach(function (el) {
            if (except && el === except) {
                return;
            }
            el.classList.remove('is-confirming');
        });
    }

    function initCrudDeleteConfirm() {
        document.addEventListener('click', function (e) {
            var openBtn = e.target.closest('[data-cc-delete-open]');
            if (openBtn) {
                e.preventDefault();
                e.stopPropagation();
                var wrap = openBtn.closest('[data-cc-crud-actions]');
                if (!wrap) {
                    return;
                }
                closeAllDeleteConfirms(wrap);
                wrap.classList.add('is-confirming');
                return;
            }

            var cancelBtn = e.target.closest('[data-cc-delete-cancel]');
            if (cancelBtn) {
                e.preventDefault();
                e.stopPropagation();
                var cancelWrap = cancelBtn.closest('[data-cc-crud-actions]');
                if (cancelWrap) {
                    cancelWrap.classList.remove('is-confirming');
                }
                return;
            }

            if (!e.target.closest('[data-cc-crud-actions]')) {
                closeAllDeleteConfirms();
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeAllDeleteConfirms();
            }
        });
    }

    function boot() {
        initSidebarToggle();
        initCrudDeleteConfirm();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
})();
