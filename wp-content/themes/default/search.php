<?php 
/** 
 * @package WordPress 
 * @subpackage Default_Theme 
 */ 

get_header(); ?>

<div id="content" class="narrowcolumn" role="main">
	<?php if (have_posts()) : ?>
	
		<h2 class="pagetitle">Résultats de recherche</h2>
		<div class="navigation">
			<div class="alignleft"><?php next_posts_link('&laquo; Articles plus anciens') ?></div>
			<div class="alignright"><?php previous_posts_link('Articles plus récents &raquo;') ?></div>
		</div>
		
		<?php while (have_posts()) : the_post(); ?>
			<div <?php post_class() ?>
				<h3 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permalien vers <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
				<small><?php the_time('l j F Y') ?></small>
				<p class="postmetadata"><?php the_tags('Mots-clefs&nbsp;: ', ', ', '<br />'); ?> Publié dans <?php the_category(', ') ?> | <?php edit_post_link('Modifier', '', ' | '); ?>  <?php comments_popup_link('Aucun commentaire »', '1 commentaire »', '% commentaires »', 'comments-link', 'Les commentaires sont fermés'); ?></p>
			</div>
		<?php endwhile; ?>
		
		<div class="navigation">
			<div class="alignleft"><?php next_posts_link('&laquo; Articles plus anciens') ?></div>
			<div class="alignright"><?php previous_posts_link('Articles plus récents &raquo;') ?></div>
		</div>
		
	<?php else : ?>
	
		<h2 class="center">Aucun article trouvé. Essayer une autre recherche ?</h2>
		<?php get_search_form(); ?>
		
	<?php endif; ?>
</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>