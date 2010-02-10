<?php //Load Variables
$slideshow_state = get_option('T_slideshow_state');
?>

<?php
if ($slideshow_state == 'On') {?>

<ul id="portfolio">
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
<?php
// this gets the image filename
$values = get_post_custom_values("slideshow");
// this checks to see if an image exists
if (isset($values[0])) {
?><li>
<a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><img class="slideshow" src="<?php $key="slideshow"; echo get_post_meta($post->ID, $key, true); ?>" alt="<?php the_title(); ?>" /></a>
</li><?php } ?> 
<?php endwhile; else: ?>
<p><?php _e('Sorry, no slideshow images are available'); ?></p>
<?php endif; ?>
</ul>