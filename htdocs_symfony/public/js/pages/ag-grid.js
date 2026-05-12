import { createGrid, ClientSideRowModelModule, ModuleRegistry } from 'https://cdn.jsdelivr.net/npm/ag-grid-community/dist/ag-grid-community.esm.min.js';
import { loadCss } from '../lib/loadAsset.js';

export async function init() {
    await Promise.all([
        loadCss('https://cdn.jsdelivr.net/npm/ag-grid-community/styles/ag-grid.css'),
        loadCss('https://cdn.jsdelivr.net/npm/ag-grid-community/styles/ag-theme-alpine.css'),
    ]);

    ModuleRegistry.registerModules([ClientSideRowModelModule]);

    const columnDefs = [
        { field: "make" },
        { field: "model" },
        { field: "price" }
    ];

    const rowData = [
        { make: "Toyota", model: "Celica", price: 35000 },
        { make: "Ford", model: "Mondeo", price: 32000 },
        { make: "Porsche", model: "Boxster", price: 72000 }
    ];

    const eGridDiv = document.querySelector('#myGrid');
    if (eGridDiv) {
        createGrid(eGridDiv, { columnDefs, rowData });
    }
}
