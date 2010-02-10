<div class="span-8 last">
	<div id="sidebar">
	
	<?php //Load Variables
  	$sidebar_state = get_option('T_sidebar_state'); 
	?> 

		<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Sidebar') ) : ?>
		<?php endif; ?>
		
	</div>
</div>