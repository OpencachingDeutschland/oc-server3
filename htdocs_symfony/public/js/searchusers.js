import { TabulatorFull as Tabulator } from '/vendor/tabulator/tabulator_esm.min.js';
import { t } from './i18n.js';

let table = null;

export function init() {
    document.getElementById('searchForm')?.addEventListener('submit', e => {
        e.preventDefault();
        runSearch();
    });
}

async function runSearch() {
    const q = document.getElementById('fq').value.trim();
    if (!q) return;

    setStatus(t('Searching…'));

    try {
        const res = await fetch('/api/users/search?' + new URLSearchParams({ q }));
        if (!res.ok) throw new Error(res.status);
        const data = await res.json();
        const items = data.items || [];

        setStatus(items.length
            ? t('Found %count% user(s)', { count: items.length })
            : t('No users found.')
        );

        renderTable(items);
    } catch {
        setStatus(t('No users found.'));
    }
}

function renderTable(data) {
    if (table) {
        table.setData(data);
        return;
    }

    table = new Tabulator('#searchResults', {
        data,
        layout: 'fitColumns',
        height: '60vh',
        renderVertical: 'virtual',
        columns: [
            {
                title: t('Username'), field: 'username',
                minWidth: 180, widthGrow: 3,
                formatter: (cell) => {
                    const row = cell.getRow().getData();
                    return `<a href="${row.profileUrl}">${cell.getValue()}</a>`;
                },
            },
            { title: t('User ID'),  field: 'userId',     width: 90,  widthGrow: 0, hozAlign: 'right' },
            { title: t('Joined'),   field: 'joinedDate',  width: 110, widthGrow: 0 },
            { title: t('Finds'),    field: 'findCount',   width: 80,  widthGrow: 0, hozAlign: 'right' },
            { title: t('Hides'),    field: 'hideCount',   width: 80,  widthGrow: 0, hozAlign: 'right' },
        ],
    });
}

function setStatus(text) {
    const el = document.getElementById('statusBar');
    if (el) el.textContent = text;
}
