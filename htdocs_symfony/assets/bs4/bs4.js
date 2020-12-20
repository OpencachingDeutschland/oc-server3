/*!
 * Import BS4 original
 */

// Starting with our APP Code
import './style/bs4.scss';

// Import thrid pary packagist
import $ from 'jquery';
import 'bootstrap';

console.log('Loaded BS4 entrypoint');


$(function () {
    $('[data-toggle="popover"]').popover()
})

$(function () {
    $('[data-toggle="tooltip"]').tooltip()
})
