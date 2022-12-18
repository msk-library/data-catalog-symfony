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

// require jQuery normally - needed by Bootstrap 4
const $ = require('jquery');

/* create global $ and jQuery variables - enable if you have js not handled by WebPack (in templates).
  global.$ = global.jQuery = $;
*/
/*
 Also set below for js not handled by Webpack in config/webpack_encore.yaml:     
  script_attributes:
    defer: true
*/
require('jquery-ui');
require('bootstrap');
require('./dataset_details');
require('./scripts.js');
require('./searching.js');
require('./tak.js');
require('./msk.js');
require('./libchat.js');
//Including select js directly on form pages instead of adding it to app.js on all app pages
//require('select2');
//require('./add_form.js'); // select2 has to be loaded before this
//require('./respond.js');

//Initialize boostrap popovers on all pages
$(document).ready(function() {
  $('[data-toggle="popover"]').popover();
});