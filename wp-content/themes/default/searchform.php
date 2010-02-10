<form method="get" id="searchform" action="<?php bloginfo('url'); ?>/">
	<div>
		<label class="hidden" for="s">Rechercher pour&nbsp;:</label>
		<input type="text" value="<?php the_search_query(); ?>" name="s" id="s" />
		<input type="submit" id="searchsubmit" value="Chercher" />
	</div>
</form>