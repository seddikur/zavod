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

    function reindexItemRows(tbody) {
        var prefix = tbody.closest('[data-cc-item-rows]').getAttribute('data-cc-item-rows-prefix') || 'RecipeItem';
        var rows = tbody.querySelectorAll('[data-cc-item-row]');
        rows.forEach(function (row, index) {
            row.querySelectorAll('[name]').forEach(function (input) {
                var name = input.getAttribute('name');
                if (!name || name.indexOf(prefix + '[') !== 0) {
                    return;
                }
                input.setAttribute('name', prefix + '[' + index + ']' + name.replace(/^[^[]+\[\d+\]/, ''));
            });
        });
    }

    function initDocumentItemRows() {
        document.querySelectorAll('[data-cc-item-rows]').forEach(function (wrap) {
            var tbody = wrap.querySelector('[data-cc-item-rows-body]');
            var tpl = wrap.querySelector('[data-cc-item-row-tpl]');
            if (!tbody || !tpl) {
                return;
            }

            var addBtn = wrap.querySelector('[data-cc-item-row-add]');
            if (addBtn) {
                addBtn.addEventListener('click', function () {
                    var html = tpl.innerHTML.replace(/__index__/g, String(tbody.querySelectorAll('[data-cc-item-row]').length));
                    var temp = document.createElement('tbody');
                    temp.innerHTML = html.trim();
                    var row = temp.firstElementChild;
                    if (row) {
                        tbody.appendChild(row);
                        reindexItemRows(tbody);
                    }
                });
            }

            wrap.addEventListener('click', function (e) {
                var removeBtn = e.target.closest('[data-cc-item-row-remove]');
                if (!removeBtn) {
                    return;
                }
                var row = removeBtn.closest('[data-cc-item-row]');
                if (!row) {
                    return;
                }
                var rows = tbody.querySelectorAll('[data-cc-item-row]');
                if (rows.length <= 1) {
                    row.querySelectorAll('select, input:not([type=hidden])').forEach(function (el) {
                        el.value = '';
                    });
                    return;
                }
                row.remove();
                reindexItemRows(tbody);
            });
        });
    }

    function boot() {
        initSidebarToggle();
        initCrudDeleteConfirm();
        initDocumentItemRows();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
})();
