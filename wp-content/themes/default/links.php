<?php 
/** 
 * @package WordPress 
 * @subpackage Default_Theme 
 */ 

/*
Template Name: Liens
*/
?>

<?php get_header(); ?>

<div id="content" class="widecolumn">
	<h2>Liens&nbsp;:</h2>
	<ul>
		<?php wp_list_bookmarks(); ?> 
	</ul>
</div>

<?php get_footer(); ?>