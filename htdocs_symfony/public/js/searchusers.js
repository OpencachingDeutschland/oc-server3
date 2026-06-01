import { TabulatorFull as Tabulator } from '/vendor/tabulator/tabulator_esm.min.js';
import { t } from './i18n.js';
import { apiFetch } from './helpers.js';

const isSupport = document.getElementById('userSearchContainer')?.dataset.isSupport === '1';

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
        const data = await apiFetch('/api/users/search?' + new URLSearchParams({ q }));
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
    const columns = [
        {
            title: t('Username'), field: 'username',
            minWidth: 160, widthGrow: 3,
            formatter: (cell) => {
                const row = cell.getRow().getData();
                return `<a href="${row.profileUrl}">${cell.getValue()}</a>`;
            },
        },
        { title: t('Finds'), field: 'findCount', width: 75, widthGrow: 0, hozAlign: 'right' },
        { title: t('Hides'), field: 'hideCount', width: 75, widthGrow: 0, hozAlign: 'right' },
    ];

    if (isSupport) {
        columns.splice(1, 0,
            { title: t('User ID'),  field: 'userId',     width: 90,  widthGrow: 0, hozAlign: 'right' },
            { title: t('Email'),    field: 'email',       minWidth: 200, widthGrow: 2 },
            { title: t('Joined'),   field: 'joinedDate',  width: 110, widthGrow: 0 }
        );
    }

    if (table) {
        table.setColumns(columns);
        table.setData(data);
        return;
    }

    table = new Tabulator('#searchResults', {
        data,
        layout: 'fitColumns',
        height: '60vh',
        renderVertical: 'virtual',
        columns,
    });
}

function setStatus(text) {
    const el = document.getElementById('statusBar');
    if (el) el.textContent = text;
}
