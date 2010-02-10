<div class="clear"></div>
</div>
</div>
<div id="footer-wrap">
<div id="footer">
<div class="span-3 append-1 small">
<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Bottom-Left') ) : ?>
<?php endif; ?>
</div>
<div class="column span-3 append-1 small">
<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Bottom-Middle') ) : ?>
<?php endif; ?>
</div>
<div class="column span-10 append-1 small">
<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Bottom-Right') ) : ?>
<?php endif; ?>
</div>
<div class="column span-5 small last">
<h3 class="sub">Credits</h3>
<p class="quiet">
		Powered by <a href="http://wordpress.org/">WordPress</a><br />
		Design by <a href="http://graphpaperpress.com">Graph Paper Press</a><br />
		<a href="<?php bloginfo('rss2_url'); ?>" class="feed">Subscribe to entries</a><br/>
		<a href="<?php bloginfo('comments_rss2_url'); ?>" class="feed">Subscribe to comments</a><br />
		All content &copy; <?php echo date("Y"); ?> by <?php bloginfo('name'); ?>
		<!-- <?php echo get_num_queries(); ?> queries. <?php timer_stop(1); ?> seconds. -->
</p>
</div>
<div class="clear"></div>
</div>
</div>
<?php wp_footer(); ?>
<?php
	$tmp_tracking_code = get_option('T_tracking_code');
	if($tmp_tracking_code != ''){
		echo stripslashes($tmp_tracking_code);
	}
?>
</body>
</html>