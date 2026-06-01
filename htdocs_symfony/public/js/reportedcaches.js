import { TabulatorFull as Tabulator } from '/vendor/tabulator/tabulator_esm.min.js';
import { t } from './i18n.js';
import { apiFetch } from './helpers.js';

let table = null;

const STATUS_CLASS = { 1: 'warning', 2: 'primary', 3: 'success' };

export function init() {
    const statusSelect = document.getElementById('fstatus');
    statusSelect?.addEventListener('change', load);
    load();
}

async function load() {
    const status = document.getElementById('fstatus')?.value ?? '0';
    setStatus(t('Searching…'));

    try {
        const data = await apiFetch('/backoffice/api/reported-caches?' + new URLSearchParams({ status }));
        const items = data.items || [];

        setStatus(items.length
            ? t('Loaded %count% report(s)', { count: items.length })
            : t('No reports found.')
        );
        renderTable(items);
    } catch {
        setStatus(t('No reports found.'));
    }
}

function renderTable(data) {
    const columns = [
        {
            title: t('ID'), field: 'id', width: 70, widthGrow: 0, hozAlign: 'right',
            formatter: (cell) => {
                const row = cell.getRow().getData();
                return `<a href="${row.detailUrl}">${cell.getValue()}</a>`;
            },
        },
        {
            title: t('Status'), field: 'statusName', width: 120, widthGrow: 0,
            formatter: (cell) => {
                const row  = cell.getRow().getData();
                const cls  = STATUS_CLASS[row.statusId] || 'secondary';
                return `<span class="badge bg-${cls}">${t(cell.getValue())}</span>`;
            },
            sorter: (a, b, _ar, _br, _p, _p2, dir) => {
                const order = { 'New': 1, 'In Progress': 2, 'Done': 3 };
                return (order[a] ?? 9) - (order[b] ?? 9);
            },
        },
        {
            title: t('OC Code'), field: 'wpOc', width: 100, widthGrow: 0,
            cssClass: 'cell-occode',
            formatter: (cell) => {
                const row = cell.getRow().getData();
                return `<a href="${row.cacheUrl}">${cell.getValue()}</a>`;
            },
        },
        { title: t('Cache'),       field: 'cacheName', minWidth: 180, widthGrow: 3 },
        {
            title: t('Owner'), field: 'owner', width: 130, widthGrow: 1,
            formatter: (cell) => {
                const row = cell.getRow().getData();
                return `<a href="${row.ownerUrl}">${cell.getValue()}</a>`;
            },
        },
        { title: t('Reported by'), field: 'reporter',  width: 130, widthGrow: 1 },
        { title: t('Date'),        field: 'date',       width: 105, widthGrow: 0 },
    ];

    if (table) {
        table.setColumns(columns);
        table.setData(data);
        return;
    }

    table = new Tabulator('#searchResults', {
        data,
        layout: 'fitColumns',
        height: '70vh',
        renderVertical: 'virtual',
        initialSort: [{ column: 'id', dir: 'desc' }],
        columns,
    });
}

function setStatus(text) {
    const el = document.getElementById('statusBar');
    if (el) el.textContent = text;
}
