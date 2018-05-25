<?php
/*
 * Provides additional theme functions
 */

define('INBOUND_MISC', true);

/*
* Ensure shortcodes are executed within the text widget
*/
add_filter('widget_text', 'do_shortcode');
