<?php
/*
Plugin Name: Freemind Viewer
Plugin URI: http://www.semeteys.org/wp-freemind
Description: Integration of Freemind Flash viewer into WordPress
Version: 1.0
Author: Raphaël Semeteys
Author URI: http://www.semeteys.org

Copyright 2009  Raphaël Semeteys  (email: raphael@semeteys.org)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

function freemind_init() {
    //Register the flashobject JS library
    wp_enqueue_script('flashobject', '/wp-content/plugins/wp-freemind/flashobject.js', array(), true);
}

add_action('plugins_loaded', 'freemind_init');

//Function to replace the [mindmap] shortcode
function handle_freemind_shortcode($atts, $content) {
$defaults_array = array(
   'width' => false,
   'height' => '600px',
   );
//Generate unique id for DIV to be replaced
$id = 'wpf'.time().rand(0,1000);
if ($content == '') $content = '/wp-content/plugins/wp-freemind/wp-freemind.mm';
$a = shortcode_atts($defaults_array, $atts);
$height = 'height:'.$a['height'];
$width = ($a['width'])?(';width:'.$a['width']):'';
return '<div id="'.$id.'" style="'.$height.$width.'">JavaScript or Flash plugin need to be activated in your browser.</div>
<script type="text/javascript">
<!--
var fo = new FlashObject("/wp-content/plugins/wp-freemind/visorFreemind.swf", "visorFreeMind", "100%", "100%", 6, "#9999ff");
fo.addVariable("initLoadFile","'.$content.'");
fo.write("'.$id.'");
//-->
</script>';
}

add_shortcode('freemind', 'handle_freemind_shortcode');
?>
