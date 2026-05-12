import { TabulatorFull as Tabulator } from '../vendor/tabulator_esm.min.js';

const LOG_ICONS = {
    1: 'found', 2: 'dnf', 3: 'note',
    7: 'attended', 8: 'will_attend',
    9: 'archived', 10: 'active', 11: 'active',
    13: 'locked_invisible', 14: 'locked_invisible',
};

function logIcon(type) {
    const name = LOG_ICONS[type] || 'note';
    return `/images/logTypes/${name}.svg`;
}

function decToDegMin(dec, isLat) {
    const dir = dec >= 0 ? (isLat ? 'N' : 'E') : (isLat ? 'S' : 'W');
    const abs = Math.abs(dec);
    const deg = Math.floor(abs);
    const min = ((abs - deg) * 60).toFixed(3);
    return `${dir} ${deg}° ${min}'`;
}

function coordStr(lat, lon) {
    return `${decToDegMin(lat, true)} &nbsp; ${decToDegMin(lon, false)}`;
}

function row(label, content, id = '') {
    const idAttr = id ? ` id="${id}"` : '';
    return `<tr><td>${label}</td><td${idAttr}>${content}</td></tr>`;
}

function sectionHeader(text) {
    return `<div class="oc-section-header">${text}</div>`;
}

function buildDetailsTable(c) {
    const iconSrc = `/images/cacheTypes/${c.typeSvg}-active-untried.svg`;
    const statusBadge = c.statusName !== 'Available'
        ? ` <span class="badge bg-warning text-dark">${c.statusName}</span>` : '';

    let rows = `
        <tr><td colspan="2" style="padding:6px 6px 2px">
            <img src="${iconSrc}" width="22" height="22" style="vertical-align:middle;margin-right:6px">
            <strong>${c.name}</strong>${statusBadge}
        </td></tr>
        ${row('Code', `<a href="https://opencaching.de/${c.wp}" target="_blank">${c.wp}</a>`)}
        ${row('Type', c.typeName)}
        ${row('Size', c.sizeName)}
        ${row('D / T', `${c.difficulty} / ${c.terrain}`)}
        ${row('Coordinates', coordStr(c.lat, c.lon))}
        ${row('Country', c.country)}
        ${row('Hidden', c.dateHidden)}
        ${row('Find count', c.findCount)}
        ${row('Recommendations', c.ratingCount)}
        ${row('Owner', `<a href="/user/${c.ownerId}">${c.ownerName}</a>`)}
    `;
    if (c.wpGc) rows += row('GC code', `<a href="https://coord.info/${c.wpGc}" target="_blank">${c.wpGc}</a>`);
    if (c.needsMaintenance) rows += row('', '<span class="oc-text-warning">⚠ Needs maintenance</span>');
    if (c.listingOutdated) rows += row('', '<span class="oc-text-warning">⚠ Listing outdated</span>');

    return rows;
}

function initLogsTable(logs) {
    new Tabulator('#logsTable', {
        data: logs,
        layout: 'fitColumns',
        pagination: logs.length > 10 ? 'local' : false,
        paginationSize: 10,
        rowHeader: false,
        columns: [
            {
                field: 'type', title: '', width: 30, hozAlign: 'center',
                formatter: (cell) => `<img class="log-icon" src="${logIcon(cell.getValue())}" title="${cell.getRow().getData().typeName}">`,
            },
            { field: 'date', title: 'Date', width: 105, formatter: (cell) => cell.getValue()?.substring(0, 10) ?? '' },
            { field: 'username', title: 'User', width: 130 },
            {
                field: 'text', title: 'Log', variableHeight: true,
                formatter: (cell) => {
                    const d = cell.getRow().getData();
                    const txt = d.textHtml ? d.text : d.text.replace(/\n/g, '<br>');
                    return `<div style="white-space:normal;padding:2px 0">${txt}</div>`;
                },
            },
        ],
    });
}

function initWaypointsTable(wpts) {
    new Tabulator('#wptTable', {
        data: wpts,
        layout: 'fitColumns',
        columns: [
            { field: 'typeName', title: 'Type', width: 110 },
            {
                field: 'lat', title: 'Coordinates', width: 220,
                formatter: (cell) => {
                    const r = cell.getRow().getData();
                    return coordStr(r.lat, r.lon);
                },
            },
            { field: 'description', title: 'Description', variableHeight: true,
              formatter: (cell) => `<div style="white-space:normal">${cell.getValue() || ''}</div>` },
        ],
    });
}

export async function init() {
    const container = document.getElementById('explore-container');
    if (!container) return;
    const wp = container.dataset.wp;
    if (!wp) return;

    let data;
    try {
        const res = await fetch(`/api/cache/${wp}`);
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        data = await res.json();
    } catch (err) {
        document.getElementById('cache-error').style.display = 'block';
        document.getElementById('cache-error').textContent = `Failed to load cache: ${err.message}`;
        document.getElementById('cache-skeleton').remove();
        document.getElementById('desc-skeleton').remove();
        return;
    }

    // -- Left column --
    document.getElementById('cache-skeleton').remove();
    const table = document.getElementById('cache-table');
    table.innerHTML = buildDetailsTable(data);
    table.style.display = '';

    // -- Description --
    document.getElementById('desc-skeleton').remove();
    if (data.desc) {
        const descEl = document.getElementById('cache-description');
        descEl.innerHTML = data.descHtml ? data.desc : data.desc.replace(/\n/g, '<br>');
        descEl.style.display = '';
    }

    // -- Hint --
    if (data.hint) {
        const hintBox = document.getElementById('hint-box');
        hintBox.textContent = data.hint;
        hintBox.addEventListener('click', () => hintBox.classList.toggle('blurred'));
        document.getElementById('hint-section').style.display = '';
    }

    // -- Attributes --
    if (data.attributes?.length) {
        const list = document.getElementById('attrib-list');
        list.innerHTML = data.attributes.map(a =>
            `<span class="attrib-item">
                <img src="/resource2/ocstyle/images/attributes/${a.icon}.png" title="${a.name}" alt="${a.name}">
                ${a.name}
            </span>`
        ).join('');
        document.getElementById('attrib-section').style.display = '';
    }

    // -- Waypoints --
    if (data.waypoints?.length) {
        document.getElementById('wpt-section').style.display = '';
        initWaypointsTable(data.waypoints);
    }

    // -- Logs --
    initLogsTable(data.logs ?? []);
}
