<?php

// Path constants
define('THEMELIB', TEMPLATEPATH . '/library');

// Create Theme Options Page
require_once (THEMELIB . '/functions/theme-options.php');

// Get Post Thumbnails and Images
include(THEMELIB . '/functions/post-images.php');

// Load widgets
include(THEMELIB . '/functions/widgets.php');

// Produces an avatar image with the hCard-compliant photo class for author info
include(THEMELIB . '/functions/author-info-avatar.php');

// Remove the WordPress Generator – via http://blog.ftwr.co.uk/archives/2007/10/06/improving-the-wordpress-generator/
function modularity_remove_generators() { return ''; }  
add_filter('the_generator','modularity_remove_generators');

?>