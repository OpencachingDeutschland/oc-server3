import { TabulatorFull as Tabulator } from 'https://unpkg.com/tabulator-tables@6.2.1/dist/js/tabulator_esm.min.js';
import { loadCss } from '../lib/loadAsset.js';

export async function init() {
    await loadCss('https://unpkg.com/tabulator-tables@6.2.1/dist/css/tabulator_bootstrap5.min.css');

    const tableData = [
        {id:1, name:"Oli Bob", age:"12", col:"red", dob:""},
        {id:2, name:"Mary May", age:"1", col:"blue", dob:"14/05/1982"},
        {id:3, name:"Christine Lobowski", age:"42", col:"green", dob:"22/05/1982"},
    ];

    new Tabulator("#example-table", {
        data: tableData,
        autoColumns: true,
    });
}
