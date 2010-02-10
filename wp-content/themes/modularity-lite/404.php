<?php get_header(); ?>
<div class="span-<?php
		$sidebar_state = get_option('T_sidebar_state');

		if($sidebar_state == "On") {
			echo "15 colborder home";
		}
		else {
			echo "24 last";
		}
		?>">
<div class="content">
		<h2>Whoops!  Whatever you are looking for cannot be found.</h2>
	</div>
	</div>
	<?php
		$sidebar_state = get_option('T_sidebar_state');

		if($sidebar_state == "On") {
			get_sidebar() ;
		}
		else {
			echo "";
		}
		?>

<!-- Begin Footer -->
<?php get_footer(); ?>