/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';

// start the Stimulus application
//import './bootstrap';

// require jQuery normally - also needed by Bootstrap 5 for compatibility
const $ = require('jquery');

// Require Bootstrap and expose it globally for template scripts
const bootstrap = require('bootstrap');
window.bootstrap = bootstrap;

/* create global $ and jQuery variables - enable if you have js not handled by WebPack (in templates).
  global.$ = global.jQuery = $;
*/
/*
 Also set below for js not handled by Webpack in config/webpack_encore.yaml:     
  script_attributes:
    defer: true
*/
require('jquery-ui');
require('./dataset_details');
require('./scripts.js');
require('./searching.js');
require('./tak.js');
require('./msk.js');
require('./libchat.js');
require('./ask_us_side_tab.js');
//Including select js directly on form pages instead of adding it to app.js on all app pages
//require('select2');
//require('./add_form.js'); // select2 has to be loaded before this
//require('./respond.js');

//Initialize bootstrap popovers on all pages (Bootstrap 5) - DISABLED
// Popover initialization is handled in specific JS files (dataset_details.js, msk.js)
// to avoid conflicts with custom hover behavior
$(document).ready(function () {
  var collapseElementList = [].slice.call(document.querySelectorAll('.collapse'));
  var collapseList = collapseElementList.map(function (collapseEl) {
    return new bootstrap.Collapse(collapseEl, {
      toggle: false
    });
  });
  // Uncomment below lines to log initialization of popovers and collapse components
  // console.log('Bootstrap popovers initialized:', popoverList.length);
  // console.log('Bootstrap collapse components initialized:', collapseList.length);

  // Initialize facet "More" functionality
  $('ul.facets-list').each(function () {
    var $list = $(this);
    var max = 4;
    var $items = $list.find('li.facet-item');
    var itemCount = $items.length;

    // console.log('Facet list found with', itemCount, 'items');

    if (itemCount > max) {
      // Hide items beyond the max (5th item and beyond) with !important
      $items.slice(max).each(function () {
        $(this).attr('style', ($(this).attr('style') || '') + '; display: none !important;');
      });
      // Add the "More" button
      $list.append('<li class="more_facets" style="cursor: pointer;">More <i class="fas fa-chevron-down"></i></li>');
      // console.log('Added "More" button to facet list with', itemCount, 'items');
    }
  });
});