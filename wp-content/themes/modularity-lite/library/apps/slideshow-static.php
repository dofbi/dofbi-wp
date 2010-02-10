<?php //Load Variables
$slideshow_state = get_option('T_slideshow_state');
?>

<?php
if ($slideshow_state == 'On') {?>

<ul id="portfolio">
<li><img src="<?php bloginfo('stylesheet_directory'); ?>/images/slideshow/image1.jpg" alt="<?php bloginfo('name'); ?>" /></li>
<li><img src="<?php bloginfo('stylesheet_directory'); ?>/images/slideshow/image2.jpg" alt="<?php bloginfo('name'); ?>" /></li>
<li><img src="<?php bloginfo('stylesheet_directory'); ?>/images/slideshow/image3.jpg" alt="<?php bloginfo('name'); ?>" /></li>
<li><img src="<?php bloginfo('stylesheet_directory'); ?>/images/slideshow/image4.jpg" alt="<?php bloginfo('name'); ?>" /></li>
<li><img src="<?php bloginfo('stylesheet_directory'); ?>/images/slideshow/image5.jpg" alt="<?php bloginfo('name'); ?>" /></li>
</ul>

<?php } ?>