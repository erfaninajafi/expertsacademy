(function () {
    'use strict';

    function normalize(value) {
        return String(value || '')
            .toLocaleLowerCase()
            .replace(/ي/g, 'ی')
            .replace(/ك/g, 'ک')
            .replace(/[\u064b-\u065f]/g, '')
            .trim();
    }

    document.addEventListener('DOMContentLoaded', function () {
        var table = document.querySelector('[data-admin-list]');
        if (!table || !table.tBodies.length) return;

        var rows = Array.prototype.slice.call(table.tBodies[0].rows);
        var search = document.querySelector('[data-admin-list-search]');
        var status = document.querySelector('[data-admin-list-status]');
        var count = document.querySelector('[data-admin-list-count]');
        var empty = document.querySelector('[data-admin-list-empty]');
        var searchForm = search && search.closest('form');

        table.classList.add('admin-list-table');
        var scroll = document.createElement('div');
        scroll.className = 'admin-list-scroll';
        table.parentNode.insertBefore(scroll, table);
        scroll.appendChild(table);

        function filter() {
            var term = normalize(search && search.value);
            var state = status && status.value;
            var visible = 0;

            rows.forEach(function (row) {
                var matchesText = !term || normalize(row.textContent).indexOf(term) !== -1;
                var matchesStatus = !state || row.getAttribute('data-admin-status') === state;
                row.hidden = !(matchesText && matchesStatus);
                if (!row.hidden) visible++;
            });

            if (count) count.textContent = visible + ' مورد از ' + rows.length + ' مورد این صفحه';
            if (empty) empty.hidden = visible !== 0;
        }

        if (search) search.addEventListener('input', filter);
        if (status) status.addEventListener('change', filter);
        if (searchForm) {
            searchForm.addEventListener('submit', function (event) {
                event.preventDefault();
                filter();
            });
        }

        filter();
    });
})();
